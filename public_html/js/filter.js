$(function(){
    function addDays(date, days) {
        const result = new Date(date);
        result.setDate(result.getDate() + days - 1);
        return result;
    }

    Date.prototype.yyyymmdd = function() {
        const mm = this.getMonth() + 1; // getMonth() is zero-based
        const dd = this.getDate();
      
        return [
            this.getFullYear(),
            (mm>9 ? '' : '0') + mm,
            (dd>9 ? '' : '0') + dd
        ].join('-');
    };

    function formatDateForUrl(dateString) {
        if (dateString && dateString.includes('-')) {
            return dateString;
        }
        try {
            const dateParts = dateString.split('-');
            const year = parseInt(dateParts[0]);
            const month = parseInt(dateParts[1]) - 1;
            const day = parseInt(dateParts[2]);

            const date = new Date(year, month, day);
            return date.yyyymmdd();
        } catch (e) {
            return dateString;
        }
    }

    const myfilter = Window.myfilter || {
        changed: false,
        region: null,
        startdate: "01-01-1901",
        enddate: "01-01-1901",
        searchtext: "",
        filterselected: Array(),
        page: 0,
        lastPage: 0
    }

myfilter.change = function(initialload = false){
    this.changed = true;
    this.searchtext = $(".event-search-header .search-text").val();
    const selectedcalendarObj = $(".calendar-content .day.selected");

    const calendardays = $(".calendar-content .day");

    const startdate = new Date(new Date().getFullYear(), 0, 1);

    if (this.region == null){
        this.region = $("#locationDropdown").attr("data-current-region-id");
    }

    const regionSlug = $("#locationDropdown").attr("data-current-region-slug");

    if (selectedcalendarObj.length > 1)
    {
        this.startdate = addDays(startdate,calendardays.index(selectedcalendarObj[0]) + 1).yyyymmdd();
        this.enddate = addDays(startdate,calendardays.index(selectedcalendarObj[selectedcalendarObj.length-1]) + 1).yyyymmdd();
    }
    if (selectedcalendarObj.length === 1) {
        this.startdate = addDays(startdate,calendardays.index(selectedcalendarObj[0]) + 1).yyyymmdd();
        this.enddate = this.startdate;
    }
    
    myfilter.page = 1;
    myfilter.lastPage = 1;

    if(!initialload)
    {
        const path = window.location.pathname;
        const dateRegex = /^\/(?:([^\/]+)\/events\/(?:\d{4}-\d{1,2}-\d{1,2})(?:_\d{4}-\d{1,2}-\d{1,2})?)?\/?$/;
        const match = path.match(dateRegex);

        if (match !== null || path === '/' || path === '') {
            // Format dates for URL
            let urlStartDate = formatDateForUrl(this.startdate);
            let urlEndDate = formatDateForUrl(this.enddate);

            // Create new URL
            let newUrl;
            if (urlStartDate === urlEndDate) {
                // Single date
                newUrl = `/${regionSlug}/events/${urlStartDate}`;
            } else {
                // Date range
                newUrl = `/${regionSlug}/events/${urlStartDate}_${urlEndDate}`;
            }

            // Update browser history without reloading the page
            window.history.pushState({}, '', newUrl);
        }

        const nf = myfilter.filterselected.filter(item => item.value !== "");

        $(".event-card-content.upcoming").empty();
        const query = new URLSearchParams();
        query.set('region', myfilter.region)
        query.set('dateFrom', myfilter.startdate)
        query.set('dateTo', myfilter.enddate)
        query.set('page', myfilter.page)
        if (typeof category !== 'undefined') {
            query.set('category', category)
        }
        query.set('f', JSON.stringify(nf))
        console.log(myfilter.startdate)
        $.getJSON("/loadmore/?" + query.toString(), function(response){
            let rhtml = "";
            myfilter.lastPage = response.lastPage

            const showMore = $("#show-more")
            myfilter.lastPage <= myfilter.page ? showMore.hide() : showMore.show()

            $.each(response.data, function( key, val ) {
                rhtml += $('#eventCardTemplate').tmpl(val).html();
            });
            $(".event-card-content.upcoming").append(rhtml);
        });
    }
}

    Window.myfilter = myfilter;

    $(".event-search-header .search-text").on("keypress",function(ev){
        if (ev.key === "Enter") {
            myfilter.change();
            // Cancel the default action, if needed
            ev.preventDefault();
            return false;
        }
    });

    $(".event-search-header .search-submit").on("click touchend",function(ev){
        myfilter.change();
        // Cancel the default action, if needed
        ev.preventDefault();
        return false;
    });


    $(".filter-content .filter-element:nth-child(1)").on("click touchend",function(ev){
        $(".filter-content .filter-element").removeClass("selected");
        $(this).addClass("selected");
        myfilter.filterselected = Array();
        myfilter.change();
    });
    /*
    $(".filter-content .filter-element").on("click touchend",function(ev){
        if(myfilter.filterselected.length>0){
            myfilter.filterselected.forEach(element => {
                $("#filterDropdown"+element.groupId).parent().addClass("selected");
            });
            myfilter.change();
        }else{
            $(".filter-content .filter-element").removeClass("selected");
            $(this).addClass("selected");
            myfilter.change();
        }
    });
    */
});