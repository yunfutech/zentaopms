function loadDocModule(libID)
{
    var link = createLink('api', 'ajaxGetChild', 'libID=' + libID + '&type=module');
    $.post(link, function(data)
    {
        $('#module').replaceWith(data);
        $('#module_chosen').remove();
        $('#module').chosen();
    });
}

/**
 * Toggle acl.
 *
 * @param  string $acl
 * @param  string $type
 * @access public
 * @return void
 */
function toggleAcl(acl, type)
{
    if(acl == 'custom')
    {
        $('#whiteListBox').removeClass('hidden');
    }
    else
    {
        $('#whiteListBox').addClass('hidden');
    }
    if(type == 'lib')
    {
        var notice  = typeof(noticeAcl[acl]) != 'undefined' ? noticeAcl[acl] : '';
        $('#noticeAcl').html(notice);
    }
    else
    {
        var notice  = typeof(noticeAcl[acl]) != 'undefined' ? noticeAcl[acl] : '';
        $('#noticeAcl').html(notice);
    }
}


$(document).ready(function()
{
    var NAME = 'zui.splitRow'; // model name.

    /* The SplitRow model class. */
    var SplitRow = function(element, options)
    {
        var that = this;
        that.name = NAME;
        var $element = that.$ = $(element);

        options = that.options = $.extend({}, SplitRow.DEFAULTS, this.$.data(), options);
        var id = options.id || $element.attr('id') || $.zui.uuid();
        var $cols = $element.children('.side-col,.main-col');
        var $firstCol = $cols.first();
        var $secondCol = $cols.eq(1);
        var $spliter = $firstCol.next('.col-spliter');
        if (!$spliter.length)
        {
            $spliter = $(options.spliter);
            if (!$spliter.parent().length)
            {
                $spliter.insertAfter($firstCol);
            }
        }
        var spliterWidth = $spliter.width();
        var minFirstColWidth = $firstCol.data('minWidth');
        var minSecondColWidth = $secondCol.data('minWidth');
        var setFirstColWidth = function(width)
        {
            var rowWidth = $element.width();
            var maxFirstWidth = rowWidth - minSecondColWidth - spliterWidth;
            width = Math.max(minFirstColWidth, Math.min(width, maxFirstWidth));
            $firstCol.width(width);
            $.zui.store.set('splitRowFirstSize:' + id, width);
        };

        var defaultWidth = $.zui.store.get('splitRowFirstSize:' + id);
        if(typeof(defaultWidth) == 'undefined')
        {
            defaultWidth = 0;
            $firstCol.find('.tabs ul.nav-tabs li').each(function(){defaultWidth += $(this).outerWidth()});
            defaultWidth += ($firstCol.find('.tabs ul.nav-tabs li').length - 1) * 10;
            defaultWidth += 30;
        }
        setFirstColWidth(defaultWidth);

        var documentEventName = '.' + id;

        var mouseDownX, isMouseDown, startFirstWidth;
        $spliter.on('mousedown', function(e)
        {
            startFirstWidth = $firstCol.width();
            mouseDownX = e.pageX;
            isMouseDown = true;
            $element.addClass('row-spliting');
            e.preventDefault();
            $(document).on('mousemove' + documentEventName, function(e)
            {
                if (isMouseDown)
                {
                    var deltaX = e.pageX - mouseDownX;
                    setFirstColWidth(startFirstWidth + deltaX);
                    e.preventDefault();
                }
                else
                {
                    $(document).off(documentEventName);
                    $element.removeClass('row-spliting');
                }
            }).on('mouseup' + documentEventName + ' mouseleave' + documentEventName, function(e)
            {
                isMouseDown = false;
                $(document).off(documentEventName);
                $element.removeClass('row-spliting');
            });
        });

        var fixColClass = function($col)
        {
            if (options.smallSize) $col.toggleClass('col-sm-size', $col.width() < options.smallSize);
            if (options.middleSize) $col.toggleClass('col-md-size', $col.width() < options.middleSize);
        };

        var resizeCols = function()
        {
            var cellHeight = $(window).height() - $('#footer').outerHeight() - $('#header').outerHeight() - 42;
            $cols.children('.panel').height(cellHeight).css('maxHeight', cellHeight).find('.panel-body').css('position', 'absolute');
            var sideHeight = cellHeight - $cols.find('.nav-tabs').height() - $cols.find('.side-footer').height() - 35;
            $cols.find('.tab-content').height(sideHeight).css('maxHeight', sideHeight).css('overflow-y', 'auto');
        };

        $(window).on('resize', resizeCols);
        $firstCol.on('resize', function(e) {fixColClass($firstCol);});
        $secondCol.on('resize', function(e) {fixColClass($secondCol);});
        fixColClass($firstCol);
        fixColClass($secondCol);
        resizeCols();
    };

    /* default options. */
    SplitRow.DEFAULTS = { spliter: '<div class="col-spliter"></div>', smallSize: 700, middleSize: 850 };

    /* Extense jquery element. */
    $.fn.splitRow = function(option)
    {
        return this.each(function()
        {
            var $this = $(this);
            var data = $this.data(NAME);
            var options = typeof option == 'object' && option;
            if(!data) $this.data(NAME, (data = new SplitRow(this, options)));
        });
    };

    SplitRow.NAME = NAME;

    $.fn.splitRow.Constructor = SplitRow;

    /* Auto call splitRow after document load complete. */
    $(function()
    {
        $('.split-row').splitRow();
    });
});

/**
 * Redirect the parent window.
 *
 * @param  int    hasLibPriv
 * @param  int    libID
 * @access public
 * @return void
 */
function redirectParentWindow(libID)
{
    var link = createLink('api', 'index', 'libID=' + libID);
    parent.location.href = link;
}

try {
    Vue.component('param-field', {
        data: function () {
            return {
                langField,
                langDesc,
                typeOptions,
            }
        },
        props: {
            value: {
                type: Object,
                default: function () {
                    return {
                        paramsType: 'object',
                        field: '',
                        desc: '',
                    }
                }
            },
        },
        watch: {
            value: {
                handler(val) {
                    this.$emit('update:value', val)
                },
                deep: true,
            }
        },
        methods: {
            add() {
                this.$emit('add', this.value)
            },
            del() {
                this.$emit('del')
            },
            addSub() {
                this.$emit('sub', {sub: this.value.sub, key: this.value.key})
            }
        },
        template: `
        <tr>
          <td class="w-300px">
            <div :style="{'padding-left': 10 * value.sub + 'px'}">
                <input type="text" :placeholder="langField" autocomplete="off" class="form-control" v-model="value.field">
            </div>
          </td>
          <td class="w-100px">
              <select class="form-control" v-model="value.paramsType">
                <option v-for="item in typeOptions" :value="item.value">{{item.label}}</option>
              </select>
          </td>
          <td class="w-80px">
              <div class="checkbox">
                <label>
                  <input type="checkbox" v-model="value.required">
                </label>
              </div>
          </td>
          <td class="w-300px">
            <input type="text" :placeholder="langDesc" autocomplete="off" class="form-control" v-model="value.desc">
          </td>
          <td>
              <button class="btn btn-link" type="button" @click="addSub" v-if="value.structType != 'formData' && ['object', 'array'].indexOf(value.paramsType) != -1">${addSubField}</button>
              <button class="btn btn-link" type="button" @click="add">${structAdd}</button>
              <button class="btn btn-link" type="button" @click="del">${structDelete}</button>
           </td>
        </tr>
    `
    })

    Vue.component('body-field', {
        data: function () {
            return {
                params: {},
                current: [],
                data: {
                    name: '',
                    desc: '',
                    structType: 'formData',
                },
                fieldKey: 1,
            }
        },
        props: {
            typeRadio: {
                type: Array,
                default: [
                    {label: 'FormData', value: 'formData'},
                    {label: 'JSON', value: 'json'},
                    {label: 'Array', value: 'array'},
                    {label: 'Object', value: 'Object'},
                ]
            },
            structType: {
                type: String,
                default: 'formData',
            },
            attr: {
                type: Array,
            },
            showType: {
                type: Boolean,
                default: true,
            }
        },
        watch: {
            structType: {
                handler(val) {
                    this.changeType()
                    this.$emit('change-type', this.structType)
                }
            },
            current: {
                handler(val) {
                    // console.log(val)
                    /* handle level field data. */
                    const attr = [];
                    val.forEach((item) => {
                        if (item.sub == 1) {
                            const newItem = {
                                ...item,
                                children: []
                            }
                            this.handleParams(val, newItem);
                            attr.push(newItem)
                        }
                    })
                    this.$emit('change', attr)
                },
                deep: true
            }
        },
        created() {
            this.current = [this.getInitField()]
            if (this.attr && this.attr.length > 0) {
                const attr = [];
                this.decodeParams(this.attr, attr)
                const current = [];
                attr.forEach(item => {
                    current.push({
                        ...item,
                        children: [],
                    })
                })
                this.current = current.splice(0);
                this.params[this.structType] = [...this.current];
            }
        },
        methods: {
            genKey() {
                let key = Date.now().toString(36)
                key += Math.random().toString(36).substr(2)
                if (this.current.findIndex(item => item.key == key) != -1) {
                    return this.genKey();
                }
                return key
            },
            getInitField() {
                const data = {
                    field: '',
                    paramsType: 'object',
                    required: '',
                    desc: '',
                    structType: 'formData',
                    sub: 1,
                    key: 1
                }
                data.structType = this.structType
                data.key = this.genKey()
                return data
            },
            handleParams(current, field) {
                current.forEach(item => {
                    if (!field.children) field.children = []
                    if (item.parentKey == field.key && field.children.findIndex(i => i.key == item.key) == -1) {
                        const newField = {
                            ...item,
                            children: [],
                        }
                        this.handleParams(current, newField)
                        field.children.push({...newField})
                    }
                })
            },
            decodeParams(data, attr) {
                if (!data) return;
                data.forEach(item => {
                    attr.push({
                        ...item,
                        children: [],
                    })
                    if (item.children && item.children.length> 0) {
                        this.decodeParams(item.children, attr)
                    }
                })
            },
            addSub(current, key, s) {
                sub = current.sub ? current.sub : 1;
                sub += 1
                if (s.sub) {
                    sub = s.sub + 1
                }
                fieldKey = s.key
                // console.log('添加子字段', JSON.stringify(s), fieldKey)
                const field = {
                    ...this.getInitField(),
                    parentKey: fieldKey,
                    sub,
                }
                current.splice(key + 1, 0, field)
            },
            add(data, sub) {
                const field = this.getInitField();
                field.sub = sub.sub;
                field.parentKey = sub.parentKey;
                data.push({...field});
            },
            del(data, index) {
                if(data.length <= 1) return;
                data.splice(index, 1)
            },
            changeType() {
                if (!this.params[this.structType] || this.params[this.structType].length > 0) {
                    this.params[this.structType] = [
                        this.getInitField()
                    ];
                }
                this.current = this.params[this.structType];
            },
            getPadding(sub) {
                var padding = 0;
                sub.forEach(item => {
                    padding += 15;
                })
                return padding + 'px';
            }
        },
        template: `
            <div>
              <div v-if="showType">
                <label class="radio-inline" v-for="item in typeRadio"><input name="type" type="radio" v-model="structType" :value="item.value">{{item.label}}</label>
              </div>
              <table class="table table-data">
                  <thead>
                    <tr>
                      <th class="w-300px">${struct_field}</th>
                      <th class="w-100px">${struct_paramsType}</th>
                      <th class="w-80px">${struct_required}</th>
                      <th class="w-300px">${struct_desc}</th>
                      <th>${struct_action}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <template v-for="(item,key) in current">
                      <param-field :value.sync="item" @add="add(current, $event)" @del="del(current, key)"  @sub="addSub(current, key, $event)"></param-field>
                    </template>
                  </tbody>
              </table>
          </div>
        `
    });
} catch(e) {}
