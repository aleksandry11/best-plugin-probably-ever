(function() {
    let shareBtn = document.getElementById('best-plugin-ever-btn');
    let modal = document.getElementById('best-plugin-share-wrap');
    let shareOverlay = document.getElementById('best-plugin-share-overlay');
    let shareCloseBtn = document.getElementById('best-plugin-share-close');
    let submitBtn = document.getElementById('best-plugin-share-submit');
    let input = document.getElementsByName('email')[0];
    let id = document.getElementsByName('product-id')[0];
    let validationNotice = document.querySelector('#best-plugin-share-label .notice');


    const shareBtnHandler = (event) => {
        if (!modal.classList.contains('active')) {
            modal.classList.add('active');
        } else {
            modal.classList.remove('active');
            validationNotice.style.display = 'none';
            input.style.borderColor = '#f1f1f1';
            input.value = '';
        }
    }


    /**
    * Open/close share modal
    */
    shareOverlay.addEventListener('click', (e) => shareBtnHandler(e));

    shareCloseBtn.addEventListener('click', (e) => shareBtnHandler(e));

    shareBtn.addEventListener('click', (e) => shareBtnHandler(e));

    /**
    * submit form handle
    */

    const validateEmail = (email) => {
        let re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    input.onchange = () => {
        submitBtn.disabled = false;
    }

    const submitHandler = (e) => {
        e.preventDefault();
        let email = input.value;

        if (validateEmail(email)) {
            //if validation passed => send request
            input.style.borderColor = '#f1f1f1';
            validationNotice.style.display = 'none';
            let xhr = new XMLHttpRequest();

            let data = {
                action: 'best_plugin_probably_ever_ajax_request',
                data: {
                    id: id.value,
                    email: input.value
                }
            };
            //prevent form for multiple submission
            submitBtn.setAttribute('disabled', 'disabled');

            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        alert('Message sent to: ' + input.value);
                        shareBtnHandler();
                        submitBtn.disabled = false;
                        input.value = '';
                        console.log(JSON.parse(xhr.responseText).message, xhr.status);
                    } else {
                        // console.log(xhr.responseText, xhr.status);
                        throw new Error(`Something went wrong!\n Server response: ${xhr.responseText}\n Status: ${xhr.status}`);
                    }
                }
            }
            xhr.open('POST', best_plugin_ever_ajax_url.url, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        
            xhr.send(JSON_to_URLEncoded(data));
        } else {
            input.style.borderColor = '#900505';
            input.value = '';
            validationNotice.style.display = 'inline';
        }
    }

    function JSON_to_URLEncoded(element,key,list){
        var list = list || [];

        if (typeof(element) === 'object') {
            for (var idx in element)
                JSON_to_URLEncoded(element[idx],key ? key+'['+idx+']' : idx,list);
        } else {
            list.push(key+'='+encodeURIComponent(element));
        }
        return list.join('&');
    }

    submitBtn.addEventListener('click', (e) => submitHandler(e));
        
    
})();