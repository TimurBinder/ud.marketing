function phoneMask() 
{
    const inputTel = document.querySelectorAll("input[type=tel]");
    inputTel.forEach((input) => {
        input.addEventListener("input", (e) => {
            e.preventDefault();
            if (input.value.length <= 2)
                input.value = e.data != null ? "+7" + e.data : "+7";
            if (e.data != null)
                if (e.data.match(/[0-9+-]/) == null || input.value.length > 12)
                    input.value = input.value.substring(0, input.value.length - 1);
        });
    });
}

function getStorageItem(item, name) 
{
    if (item == "" || item == null) 
    {
        if (localStorage.getItem(name) != undefined)
            item = localStorage.getItem(name);
    } 
    else 
    {
        localStorage.setItem(name, item);
    }

    return item;
}

function removeStorageItem(name)
{
    if (localStorage.getItem(name) != undefined)
        localStorage.removeItem(name);
}

function removeUtmFromStorage() 
{
    removeStorageItem('utm_source');
    removeStorageItem('utm_content');
    removeStorageItem('utm_medium');
    removeStorageItem('utm_campaign');
    removeStorageItem('utm_term');
}

function showLoading() 
{
    let block = document.createElement("div");
    block.className = "loading";
    let loading = document.createElement("div");
    loading.innerHTML =
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/><path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"><animateTransform attributeName="transform" dur="1s" from="0 12 12" repeatCount="indefinite" to="360 12 12" type="rotate"/></path></svg><style>.loading{position: absolute; width: 100%;height: 100%;left: 0;top: 0;background-color: #0000001a;z-index: 10000; display: flex; align-items: center; justify-content: center;}</style>';
    block.insertAdjacentElement("beforeend", loading);
    document.querySelector("body").insertAdjacentElement("beforeend", block);
}

function tryGetUrlParameter(name)
{
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return (results === null || results == '') ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

function fetchLead(form, params, callback = () => {}, phonePattern = "^\\+7\\s\\(\\d{3}\\)\\s\\d{3}\\s\\d{2}\\s\\d{2}$", fetchUrl = 'https://data.tech-ud72.ru', errorCallback = () => {}) 
{
    const phoneRegex = new RegExp(phonePattern,"g")

    if (!phoneRegex.test(params.phone))
    {
        console.log('error');
        console.log(params.phone);
        errorCallback();
        return;
    }

    showLoading();

    let dateTime = localStorage.getItem(params.phone);
    let oneHourInMilliseconds = 60 * 60 * 1000;

    if (dateTime == undefined || new Date().getTime() - parseInt(dateTime) > oneHourInMilliseconds) {
        localStorage.removeItem(params.phone);
        localStorage.setItem(params.phone, new Date().getTime());
    } else {
        window.location.replace(window.location.href);
        return;
    }

    fetch(fetchUrl, {
        mode: "no-cors",
        method: "POST",
        headers: {
          "Content-Type": "application/json;charset=utf-8",
        },
        body: JSON.stringify(params), 
    }).then(res => {
        callback();
    }); 
} 