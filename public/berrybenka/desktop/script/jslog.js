try {
  window.onerror = function(msg, url, lineNo, columnNo, error) {
    if (confirm("Press a button!") == true) {
        window.onbeforeunload = function(){
            return 'Are you sure you want to leave?';
        };
    }
    //api url
    var apiUrl = '/jslog';
    //suppress browser error messages
    var getURL = window.location.href;
    var suppressErrors = false;    
    $.ajax({
        url: apiUrl,
        method: 'POST',        
        data: {
            _token: $("input[name='_token']").val(),
            errorMsg: msg,
            URL: getURL,
            errorLine: lineNo,
            column:  columnNo,
            errObject: JSON.stringify(error),
            queryString: document.location.search,           
            referrer: document.referrer,
            userAgent: navigator.userAgent
        }
    });
    return suppressErrors;
  };
} catch(e) { }
