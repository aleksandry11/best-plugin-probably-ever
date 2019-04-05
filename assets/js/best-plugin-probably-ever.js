(function() {
    let shareBtn = document.getElementById('best-plugin-ever-btn');
    /**
     * Run this script only on the page with inserted shortcode => page with share button
     */

        let modal = document.getElementById('best-plugin-share-wrap');
        let shareOverlay = document.getElementById('best-plugin-share-overlay');
        let shareCloseBtn = document.getElementById('best-plugin-share-close');
        let submitBtn = document.getElementById('best-plugin-ever-submit');

        const shareBtnHandler = (event) => {
            if (!modal.classList.contains('active')) {
                modal.classList.add('active');
            } else {
                modal.classList.remove('active');
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

        const submitHandler = (e) => {
            let xhr = new XMLHttpRequest();

            xhr.open('POST', 'http://localhost', true);
        }

        submitBtn.addEventListener('click', (e) => submitHandler(e));
        
    
})();