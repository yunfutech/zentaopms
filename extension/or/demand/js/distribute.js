$(function()
{
    var productID = $('#product').val();
    loadProductBranches(productID);
})

/**
 * Load roadmap.
 *
 * @param  productID $productID
 * @param  branch $branch
 * @access public
 * @return void
 */
function loadRoadmap(productID, branch)
{
    var link = createLink('demand', 'ajaxGetRoadmaps', 'productID=' + productID + '&branch=' + branch + '&param=distributable');
    $.post(link, function(html)
    {
        $('#roadmap').replaceWith(html);
        $('#roadmapBox .picker').remove();
        $('#roadmap').picker();

        var optionCount = $(html).find('option').length;
        if (optionCount < 2) {
            $('.newRoadmap').removeClass('hidden');
        }
        else
        {
            $('.newRoadmap').addClass('hidden');
        }
    });
}

/**
 * Load branches when change product.
 *
 * @param  int   $productID
 * @access public
 * @return void
 */
function loadProductBranches(productID)
{
    var param = 'all';
    $('#branch').remove();
    $('#branch_chosen').remove();

    $.get(createLink('branch', 'ajaxGetBranches', "productID=" + productID + "&oldBranch=0&param=active"), function(data)
    {
        var $product = $('#product');
        var $inputGroup = $product.closest('.input-group');
        $inputGroup.find('.input-group-addon').toggleClass('hidden', !data);
        if(data)
        {
            $inputGroup.append(data);
            $('#branch').css('width', config.currentMethod == 'create' ? '120px' : '65px').chosen();
        }
        $inputGroup.fixInputGroup();

        var productID = $('#product').val();
        var branch    = $('#branch').val();
        if(branch === undefined) branch = '';
        loadRoadmap(productID, branch);
    })
}

/**
 * Load branch.
 *
 * @param  obj $obj
 * @access public
 * @return void
 */
function loadBranch(obj)
{
    var productID = $('#product').val();
    var branch    = $('#branch').val();
    loadRoadmap(productID, branch);
}

/**
 * Add new product.
 *
 * @param  obj $obj
 * @access public
 * @return void
 */
function addNewProduct(obj)
{
    if($(obj).attr('checked'))
    {
        /* Hide product and plan dropdown controls. */
        $('.productsBox .row').addClass('hidden');
        $('.productsBox .row .input-group').find('select').attr('disabled', true).trigger("chosen:updated");

        /* Displays the input box for creating a product. */
        $("[name^='newProduct']").prop('checked', true);
        $('#productName').removeAttr('disabled', true);
        $('.productsBox .addProduct').removeClass('hidden');
    }
    else
    {
        /* Show product and product dropdown controls. */
        $('.productsBox .row').removeClass('hidden');
        $('.productsBox .row .input-group').find('select').removeAttr('disabled').trigger("chosen:updated");

        /* Hide the input box for creating a product. */
        $("[name^='newProduct']").prop('checked', false);
        $('#productName').attr('disabled', true);
        $('.productsBox .addProduct').addClass('hidden');
    }
}

/**
 * Add new roadmap.
 *
 * @param  obj $obj
 * @access public
 * @return void
 */
function addNewRoadmap(obj)
{
    if($(obj).attr('checked'))
    {
        $('.roadmapStart').removeClass('hidden').find('#roadmapStart').attr('disabled', false);
        $('.roadmapEnd').removeClass('hidden').find('#roadmapEnd').attr('disabled', false);

        $('.roadmapBox .row').addClass('hidden');
        $('.roadmapBox .row .input-group').find('select').attr('disabled', true).trigger("chosen:updated");
        $("[name^='newRoadmap']").prop('checked', true);
        $('#roadmapName').removeAttr('disabled', true);
        $('.roadmapBox .addRoadmap').removeClass('hidden');
    }
    else
    {
        $('.roadmapStart').addClass('hidden').find('#roadmapStart').attr('disabled', true);
        $('.roadmapEnd').addClass('hidden').find('#roadmapEnd').attr('disabled', true);

        $('.roadmapBox .row').removeClass('hidden');
        $('.roadmapBox .row .input-group').find('select').removeAttr('disabled').trigger("chosen:updated");
        $("[name^='newRoadmap']").prop('checked', false);
        $('#roadmapName').attr('disabled', true);
        $('.roadmapBox .addRoadmap').addClass('hidden');
    }
}
