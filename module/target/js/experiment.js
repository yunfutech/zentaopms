$("#submit").click(function(){
    let flag = false
    let res = $('#dataform').find('input[name="precision"], input[name="recall"], input[name="f1"]').each(function()
    {
        let value = $(this).val()
        if(/^[01]\.\d{3}$/.test(value) === false)
        {
            $(this).focus()
            flag = true
            return false
        }
    });
    if (flag) {
        return false
    }
})