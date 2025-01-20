/**
 * The purpose of this script is to enhance all Autocomplete (select2) widgets with a [+] button functionality. It only
 * relies on Javascript and does not imply any changes on your CRUD controllers. The business logic is:
 *
 *     1. It finds all Select2 widgets. It also listens for events when a new Collection row is added
 *     2. If those have an "autocomplete" URL that is derived from the AssociationField::OPTION_CRUD_CONTROLLER setting
 *     3. Use the "new" action from the same controller to fetch the record creation form. Show that in a popup
 *     4. Upon submission the script waits for a redirect to edit that record (saveAndContinue action)
 *     5. It extracts the Entity Id from the above URL and uses it to search the record through the CRUD Controller
 *          - This is done to get the __toString() representation of the Entity, which may be composed of a few fields
 *     6. Adds the ID and Text representation to the Autocomplete and selects it
 *
 * Known issues:
 *     - It's not fully compliant with error handling, etc. i.e. the form might get stuck in case of submit errors
 *     - The forms shown in the popup should be simple - CollectionFields and Autocompletes do not work properly (JS?)
 *     - There is no backend validation Out of the box - only the html5 one
 *
 * @author Ilian Madzharov <imadzharov88@gmail.com>
 */

window.addEventListener('load', function() {
    enhanceAutocompleteInputs();
    $.featherlight();
    document.addEventListener('ea.collection.item-added', enhanceAutocompleteInputs);//for CollectionField Autocompletes
});

const loader = $('<div class="spinner-border m-5" role="status"><span class="sr-only">Loading...</span></div>');

function enhanceAutocompleteInputs(ev) {
    $('select.form-select').each(function () {
        let $this = $(this),
            autocompleteUrl = $this.data('ea-autocomplete-endpoint-url');

        if (undefined !== autocompleteUrl && undefined === $this.attr('data-enhanced')) {
            $this.attr('data-enhanced', true);//Prevent adding [+] more than once (e.g. when using CollectionField)

            //Add [+] Btn & apply some styles
            const plusBtn = $('<button type="button" class="btn btn-link"><i class="fas fa-plus"></i></button>');
            $this.parent().append(plusBtn);
            $this.parent().find('.select2-container--bootstrap').css('display', 'inline-block');

            //get Capitalized entity (used to identify the form later); Collection rows ids are handled as well
            let association = getAssociationName($this.attr('id'));
            plusBtn.click(function() {
                showPopupLoader();

                //Fetch the form for the "new" action from the same CRUD controller
                let crudNewUrl = autocompleteUrl.replace('crudAction=autocomplete', 'crudAction=new');
                $.get(crudNewUrl, function(data) {
                    //Save BTN is not part of the form, find it & add it. The name is essential!
                    const saveBtn = $(data).find('.action-saveAndReturn.btn.btn-primary.action-save').val('saveAndContinue'),
                        hiddenInput = $('<input/>', {type: 'hidden', name: saveBtn.attr('name'), value: saveBtn.val()}),
                        title = $(data).find('div.content-header-title h1.title').text();

                    let form = $(data).find('form#new-' + association + '-form')
                        .prepend('<h5>' + title + '</h5>')
                        .css('min-width', '450px')
                        .append(saveBtn)
                        .append(hiddenInput)
                        .submit(function(ev) {
                            ev.preventDefault();
                            showPopupLoader();

                            //TODO -> check if file upload fields work properly
                            const data = new URLSearchParams();
                            for (const pair of new FormData(this)) {
                                data.append(pair[0], pair[1]);
                            }

                            //Use Fetch API, since JQuery doesn't catch redirects. Refactor to use Fetch everywhere?
                            fetch(crudNewUrl, {
                                method: 'post',
                                body: data,
                            })
                                .then(response => {
                                    /*
                                    * TODO
                                    *  Backend validation is not done Out of the Box -> in case there are duplicate records
                                    *  or other errors, this logic will not work out.
                                    * */
                                    if (response.redirected) {
                                        const urlParams = new URLSearchParams(response.url);
                                        const entityId = urlParams.get('entityId');

                                        //Fetch the entity by ID, add it to the Autocomplete, select it & close the popup
                                        $.get(autocompleteUrl + "&query=" + entityId, function( data ) {
                                            const newEntry = data.results.filter(res => res.entityId == entityId).pop();

                                            const newOption = new Option(newEntry.entityAsString, newEntry.entityId, true, true);
                                            $this.append(newOption).trigger('change');

                                            $.featherlight.close();
                                        });
                                    }
                                });
                        });

                    setPopupContent(form);
                });
            });
        }
    });
}

function getAssociationName(identifier) {
    let segment = 1,//normal autocomplete ID: <main entity>_<Association_peroperty>_autocomplete
        array = identifier.split('_');

    if (4 == array.length && !isNaN(array[2])) {
        //<main entity>_<collection properties>_<ID of the collection row>_<Association_peroperty>
        segment = 3;
    }

    return array[segment].charAt(0).toUpperCase() + array[segment].slice(1);//get capitalized Entity
}

function showPopupLoader() {
    $.featherlight.close();

    $.featherlight(loader, {
        closeOnClick: false,
        closeOnEsc: false,
        closeIcon: ''
    });
    $('.featherlight-content').addClass('overflow-hidden');
}

function setPopupContent(content) {
    $.featherlight.close();

    $.featherlight(content, {
        closeOnClick: 'background',
        closeOnEsc: true,
        closeIcon: '&#10005;'
    });
}