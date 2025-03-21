<?php
declare(strict_types=1);
namespace zin;

class pasteDialog extends wg
{
    /**
     * @var mixed[]
     */
    protected static $defineProps = array(
        'field: string',              // 表单中多行录入列的字段名，必选
        'title?: string',             // 弹窗的标题，默认为“多行录入”
        'name?: string="importLines"' // 多行文本控件名称，默认为“importLines”
    );

    /**
     * @return string|false
     */
    public static function getPageCSS()
    {
        return file_get_contents(__DIR__ . DS . 'css' . DS . 'v1.css');
    }

    /**
     * @return string|false
     */
    public static function getPageJS()
    {
        return file_get_contents(__DIR__ . DS . 'js' . DS . 'v1.js');
    }

    protected function build(): wg
    {
        global $lang;
        $field = $this->prop('field');
        $title = $this->prop('title');
        $name  = $this->prop('name');
        if(empty($title)) $title = $lang->pasteText;

        return modal
        (
            set::id('paste-dialog'),
            set::title($title),
            set::footerClass('center pt-2'),
            set::footerActions
            (
                array
                (
                    btn
                    (
                        setClass('btn btn-wide primary'),
                        on::click("importLines(target, '$field')"),
                        $lang->save
                    )
                )
            ),
            textarea
            (
                setID($name),
                setClass('mt-2'),
                set::name($name),
                set::placeholder($lang->pasteTextInfo)
            )
        );
    }
}
