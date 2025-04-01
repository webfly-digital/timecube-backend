<?if (!isset($_REQUEST['PROPERTY'])) {?>
    <script>
        function decodeEntities(encodedString) {
            var textArea = document.createElement('textarea');
            textArea.innerHTML = encodedString;
            return textArea.value;
        }
        $(function() {
            if(window.formRequestMsg)
                $('textarea[data-code="USER_COMMENT_AREA"]').html(decodeEntities(decodeEntities(formRequestMsg)));
            if(window.trade_id)
                $('input[data-code="TRADE_ID_HIDDEN"]').val(trade_id); 
            if(window.trade_name)
                $('input[data-code="TRADE_NAME_HIDDEN"]').val(trade_name);
            if(window.trade_link)
                $('input[data-code="TRADE_LINK_HIDDEN"]').val(trade_link); 
            if (window.current_offer_id && parseInt(current_offer_id) > 0)
                $('input[data-code="OFFER_ID_HIDDEN"]').val(current_offer_id); 
        });
    </script>
<? } ?>