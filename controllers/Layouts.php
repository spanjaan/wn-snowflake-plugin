<?php namespace SpAnjaan\Snowflake\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use SpAnjaan\Snowflake\Classes\EnumFieldType;
use SpAnjaan\Snowflake\Models\Layout;
use SpAnjaan\Snowflake\Models\Settings;
use SpAnjaan\Snowflake\Widgets\Dropdown;

/**
 * Layouts Backend Controller
 */
class Layouts extends Controller
{
    /**
     * @var array Behaviors that are implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
    ];

    /**
     * @var string Configuration file for the `FormController` behavior.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string Configuration file for the `ListController` behavior.
     */
    public $listConfig = 'config_list.yaml';

    protected $dropdownWidget;

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('SpAnjaan.Snowflake', 'snowflake', 'layouts');
        $this->dropdownWidget = new Dropdown($this);
        $this->dropdownWidget->alias = 'layouts';
        $this->dropdownWidget->setListItems(Layout::lists('filename', 'id'));
        $this->dropdownWidget->bindToController();
    }

    public function listExtendQuery($query)
    {
        $query->withLayout($this->dropdownWidget->getActiveIndex());
    }

    public function formExtendFieldsBefore($form): void
    {
        $mdMode = Settings::get('markdown_mode') ? 'split' : 'tab';

        $typeId = $form->model->attributes['type_id'] ?? null;

        $fields = match ($typeId) {
            EnumFieldType::Text => ['content' => ['type' => 'text', 'label' => 'Content', 'span' => 'full']],
            EnumFieldType::Link => ['content' => ['type' => 'text', 'label' => 'Link', 'span' => 'full']],
            EnumFieldType::Image => [
                'image' => ['type' => 'fileupload', 'label' => 'Image', 'mode' => 'image', 'span' => 'left', 'useCaption' => false],
                'alt' => ['type' => 'text', 'label' => 'Alt Attribute', 'span' => 'left']
            ],
            EnumFieldType::Color => ['content' => ['type' => 'colorpicker', 'span' => 'left', 'label' => 'Color']],
            EnumFieldType::Markdown => ['content' => ['type' => 'markdown', 'mode' => $mdMode, 'size' => 'huge']],
            EnumFieldType::RichEditor => ['content' => ['type' => 'richeditor', 'size' => 'huge']],
            EnumFieldType::Code => ['content' => ['type' => 'codeeditor', 'size' => 'huge']],
            EnumFieldType::Date => ['content' => ['type' => 'datepicker', 'mode' => 'date', 'span' => 'left']],
            EnumFieldType::Textarea => ['content' => ['type' => 'textarea', 'label' => 'Content', 'size' => 'huge']],
            EnumFieldType::File => [
                'file' => ['type' => 'fileupload', 'label' => 'File', 'mode' => 'file', 'span' => 'left'],
                'filename' => ['type' => 'text', 'label' => 'Filename', 'span' => 'left']
            ],
            EnumFieldType::MediaImage => [
                'content' => ['type' => 'mediafinder', 'label' => 'Image (Media Manager)', 'mode' => 'image', 'span' => 'left'],
                'alt' => ['type' => 'text', 'label' => 'Alt Attribute', 'span' => 'left']
            ],
            EnumFieldType::MediaFile => [
                'content' => ['type' => 'mediafinder', 'label' => 'File (Media Manager)', 'mode' => 'file', 'span' => 'left'],
                'filename' => ['type' => 'text', 'label' => 'Filename', 'span' => 'left']
            ],
            default => []
        };

        $form->fields = array_merge($form->fields, $fields);
    }
}
