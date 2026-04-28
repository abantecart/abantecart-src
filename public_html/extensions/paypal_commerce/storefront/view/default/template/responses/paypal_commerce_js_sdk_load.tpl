<script type="text/javascript">
function loadPaypalScript(url, callback,formElm) {
    const script = document.createElement("script");
    script.type = "text/javascript";
    script.setAttribute("data-client-token", <?php js_echo($client_token)?>);
    script.setAttribute("data-partner-attribution-id", atob(<?php js_echo($bn_code);?>));
    const pageType = <?php js_echo($pageType);?>;
    if (pageType) {
        script.setAttribute("data-page-type", pageType);
    }
    script.addEventListener('error', function (e) {
        formElm.before(
            '<div class="alert alert-warning">' +
            '<i class="fa fa-exclamation fa-fw">' +
            '</i> Apologies, unable to load the PayPal script. Please try later or choose another payment method.</div>');
        $('#div-preloader').hide();
    });
    if (script.readyState) {
        // For IE
        script.onreadystatechange = function () {
            if (script.readyState === "loaded" ||
                script.readyState === "complete") {
                script.onreadystatechange = null;
                callback();
            }
        };
    } else {
        // For other browsers
        script.onload = function () {
            callback();
        };
    }

    script.src = url;
    try {
        document.getElementsByTagName('head')[0].appendChild(script);
    } catch (e) {
        console.log(e);
    }
}

function parsePayPalErrorMessage(errMessage) {
    try {
        if (!errMessage) return null;
        const jsonStart = errMessage.indexOf('{');
        if (jsonStart === -1) {
            return {
                description: String(errMessage)
            };
        }

        const rawJson = errMessage.slice(jsonStart);
        const parsed = JSON.parse(rawJson);

        return {
            name: parsed.name,
            issue: parsed.details?.[0]?.issue,
            field: parsed.details?.[0]?.field,
            description: parsed.details?.[0]?.description,
            debugId: parsed.debug_id,
            link: parsed.links?.[0]?.href,
            raw: parsed
        };
    } catch (e) {
        return {
            error: 'Failed to parse PayPal error JSON',
            description: String(errMessage || 'An unknown error occurred.'),
            rawMessage: errMessage
        };
    }
}
</script>