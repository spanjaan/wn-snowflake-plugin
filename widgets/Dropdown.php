<?php namespace SpAnjaan\Snowflake\Widgets;

use Backend\Classes\WidgetBase;

/**
 * Dropdown Widget
 */
class Dropdown extends WidgetBase
{
    /**
     * Default error message for an empty items list.
     */
    private const DEFAULT_ERROR = 'No page has been added so far.';

    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'dropdown';

    /**
     * @var string Default error message for an empty items list.
     */
    protected $defaultError;

    /**
     * @var int The first item index that shows in the button.
     */
    protected $index = 1;

    /**
     * @var array Cache list of items to show in the dropdown.
     */
    protected $listItems = [];

    /**
     * Constructor.
     *
     * @param mixed $controller
     * @param array $listItems
     * @param string $defaultError
     */
    public function __construct($controller, array $listItems = [], string $defaultError = self::DEFAULT_ERROR)
    {
        parent::__construct($controller);
        $this->listItems = $listItems;
        $this->defaultError = $defaultError;
    }

    /**
     * Renders the widget.
     *
     * @return string
     */
    public function render(): string
    {
        $this->prepareVars();
        return $this->makePartial('dropdown');
    }

    /**
     * Prepares the view data.
     */
    public function prepareVars(): void
    {
        $this->vars['index'] = $this->getActiveIndex();
        $this->vars['items'] = $this->getListItems();
        $this->vars['error_message'] = $this->defaultError;
    }

    /**
     * Handles item change event.
     *
     * @return array
     */
    public function onItemChange(): array
    {
        $this->setActiveIndex(post('index'));
        $widgetId = '#' . $this->getId();
        $listId = '#' . $this->controller->listGetWidget()->getId();
        $listRefreshData = $this->controller->listRefresh();

        return [
            $listId => $listRefreshData[$listId],
            $widgetId => $this->makePartial('dropdown', [
                'index' => $this->getActiveIndex(),
                'items' => $this->getListItems(),
            ]),
        ];
    }

    /**
     * Gets the list items array for this widget instance.
     *
     * @return array
     */
    public function getListItems(): array
    {
        return $this->listItems;
    }

    /**
     * Sets the list items array for this widget instance.
     *
     * @param array $listItems
     */
    public function setListItems(array $listItems): void
    {
        $this->listItems = $listItems;
    }

    /**
     * Gets the error message for this widget instance.
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->defaultError;
    }

    /**
     * Sets the error message for this widget instance.
     *
     * @param string $message
     */
    public function setErrorMessage(string $message): void
    {
        $this->defaultError = $message;
    }

    /**
     * Returns an active index for this widget instance.
     *
     * @return int
     */
    public function getActiveIndex(): int
    {
        $this->index = $this->getSession('index', 1);
        return isset($this->listItems[$this->index])
            ? $this->index
            : (array_key_first($this->listItems) ?: 1);
    }

    /**
     * Sets an active index for this widget instance.
     *
     * @param int $index
     */
    public function setActiveIndex(int $index): void
    {
        if ($index) {
            $this->putSession('index', $index);
        } else {
            $this->resetSession();
        }

        $this->index = $index;
    }

    /**
     * Returns a value suitable for the field name property.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->alias . '[index]';
    }
}
