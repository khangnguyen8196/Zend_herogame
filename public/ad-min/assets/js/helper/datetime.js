$(function () {
    pages.datetime.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	datetime: {
		US_DATE_FMT: 'mm/dd/yyyy',
        init: function () {
        },
    	format: function(date, mask) {
    		if (!date) return '';
    	        
    	    if (typeof date === 'string') {
    	    	date = new Date(date);
    	    }
    	
    	    return mask.replace(/(yyyy|ee|mm|dd|hh|nn|ss)/gi,
    	        function($1)
    	        {
    	            switch ($1.toLowerCase())
    	            {
    		            case 'yyyy': return date.getFullYear();
    		            case 'ee': 	 return date.getFullYear() % 100;
    		            case 'mm':   return ('0' + (date.getMonth() + 1)).slice(-2);
    		            case 'dd':   return ('0' + date.getDate()).slice(-2);
    		            case 'hh':   return ('0' + (hour = date.getHours() % 24)).slice(-2);
    		            case 'nn':   return ('0' + date.getMinutes()).slice(-2);
    		            case 'ss':   return ('0' + date.getSeconds()).slice(-2);
    	            }
    	        }
    	    );
    	},
    	formatUTC: function(date, mask) {
    		if (!date) return '';
    	        
    	    if (typeof date === 'string') {
    	    	date = new Date(date);
    	    }
    	    	    
    	    return mask.replace(/(yyyy|ee|mm|dd|hh|nn|ss)/gi,
    	        function($1)
    	        {
    	            switch ($1.toLowerCase())
    	            {
    	            case 'yyyy': return date.getUTCFullYear();
    	            case 'ee': return date.getUTCFullYear() % 100;
    	            case 'mm':   return ('0' + (date.getUTCMonth() + 1)).slice(-2);
    	            case 'dd':   return ('0' + date.getUTCDate()).slice(-2);
    	            case 'hh':   return ('0' + (hour = date.getUTCHours() % 24)).slice(-2);
    	            case 'nn':   return ('0' + date.getUTCMinutes()).slice(-2);
    	            case 'ss':   return ('0' + date.getUTCSeconds()).slice(-2);
    	            }
    	        }
    	    );
    	},
    	
    	formatUsDate: function(date) {
    		var me = this;
    		return me.format(date, me.US_DATE_FMT);
    	},
    	
    	getTimezone: function() {
    		return (new Date()).getTimezoneOffset() / (-60);
    	},
    	
    	addHours: function(date, hour) {
    		if (!date.valueOf())
    	        return '';
    	        
    	    date = new Date(date);
    	    
    	    return new Date(date.setHours(date.getHours() + hour));
    	},
    	
    	addDays: function(date, day) {
    		if (!date.valueOf())
    	        return '';
    	        
    	    date = new Date(date);
    	    
    	    return new Date(date.setDate(date.getDate() + day));
    	},
    	
    	addMonths: function(date, month) {
    		if (!date.valueOf())
    	        return '';
    	        
    	    date = new Date(date);
    	    
    	    return new Date(date.setMonth(date.getMonth() + month));
    	},
    	
    	addYears: function(date, year) {
    		if (!date.valueOf())
    	        return '';
    	        
    	    date = new Date(date);
    	    
    	    return new Date(date.setFullYear(date.getFullYear() + year));
    	},
    	
    	parseIso8601Datetime: function( date, isDateTime , format ) {
    		var formated = "yyyy/mm/dd";
    		if( isDateTime == true ){
    			formated += " hh:nn:ss";
    		}
    		if( pages.core.isDefined( format ) && format != "" ) { // change format type  / with -
    			formated = format;
    		}
    		if ( pages.core.isDefined( date ) && date != "" ) {
    			var parsedDate = new Date( Date.parse( date ) );
    			return this.format( date, formated );
    		} else {
    			return "";
    		}
    	},
    	
    	parseIso8601DatetimeUTC: function( date, isDateTime , format) {
    		var formated = "yyyy/mm/dd";
    		if( isDateTime == true ){
    			formated += " hh:nn:ss";
    		}
    		if( pages.core.isDefined( format ) && format != "" ) { // change format type  / with -
    			formated = format;
    		}
    		if ( pages.core.isDefined( date ) && date != "" ) {
    			var parsedDate = new Date( Date.parse( date ) );
    			return this.formatUTC( parsedDate, formated );
    		} else {
    			return "";
    		}
    	},
    	
    	parseIsoDatetime: function( date, isDateTime , format ) {
    		var formated = "yyyy/mm/dd";
    		if ( isDateTime == true ) {
    			formated = formated + " hh:nn:ss";
    		}
    		if( pages.core.isDefined( format ) && format != "" ) { // change format type  / with -
    			formated = format;
    		}
    		if ( pages.core.isDefined( date ) && date != "" ) {
    			var arr = date.split(/-|\s|:/);// split string and create array.
    			var parsedDate = new Date(arr[0], arr[1] -1, arr[2], arr[3], arr[4], arr[5]); // decrease month value by 1
    			return this.format( parsedDate, formated );
    		} else {
    			return "";
    		}
    	},
    	
    	parseIsoDatetimeUTC: function( date, isDateTime , format ) {
    		var formated = "yyyy/mm/dd";
    		if ( isDateTime == true ) {
    			formated = formated + " hh:nn:ss";
    		}
    		if( pages.core.isDefined( format ) && format != "" ) { // change format type  / with -
    			formated = format;
    		}
    		if ( pages.core.isDefined( date ) && date != "" ) {
    			var arr = date.split(/-|\s|:/);// split string and create array.
    			var parsedDate = new Date(arr[0], arr[1] -1, arr[2], arr[3], arr[4], arr[5]); // decrease month value by 1
    			return this.formatUTC( parsedDate, formated );
    		} else {
    			return "";
    		}
    	},
    	
    	formatValueSearchDate: function( date , format ) {
    		if( pages.core.isDefined( date ) && date != "" ){
    	    	var tempdate = new Date( date );
    	    	return this.format( tempdate , format );
        	} else {
        		return date;
        	}
    	}
    }
});