<?php declare(strict_types = 1);

namespace Contributte\Datagrid\InlineEdit;

use Contributte\Datagrid\Datagrid;
use Contributte\Datagrid\Row;
use Contributte\Datagrid\Traits\TButtonClass;
use Contributte\Datagrid\Traits\TButtonIcon;
use Contributte\Datagrid\Traits\TButtonText;
use Contributte\Datagrid\Traits\TButtonTitle;
use Contributte\Datagrid\Traits\TButtonTryAddIcon;
use Nette\Forms\Container;
use Nette\Forms\Controls\Checkbox;
use Nette\Forms\Controls\CheckboxList;
use Nette\Forms\Controls\SelectBox;
use Nette\Forms\Controls\TextBase;
use Nette\SmartObject;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;

/**
 * @method onSubmit($id, ArrayHash $values)
 * @method onControlAdd(Container $container)
 * @method onControlAfterAdd(Container $container)
 * @method onSetDefaults(Container $container, $item)
 * @method onCustomRedraw(string $buttonName)
 */
class InlineEdit
{

	use SmartObject;
	use TButtonTryAddIcon;
	use TButtonIcon;
	use TButtonClass;
	use TButtonTitle;
	use TButtonText;

	/** @var array|callable[] */
	public array $onSubmit = [];

	/** @var array|callable[] */
	public array $onControlAdd = [];

	/** @var array|callable[] */
	public array $onControlAfterAdd = [];

	/** @var array|callable[] */
	public array $onSetDefaults = [];

	/** @var array|callable[] */
	public array $onCustomRedraw = [];

	protected mixed $itemID = null;

	/**
	 * Inline adding - render on the top or in the bottom?
	 */
	protected bool $positionTop = false;

	/**
	 * Columns that are not edited can displey normal value instaad of nothing..
	 */
	protected bool $showNonEditingColumns = true;

	public function __construct(protected Datagrid $grid, protected ?string $primaryWhereColumn = null)
	{
		$this->title = 'contributte_datagrid.edit';
		$this->class = sprintf('btn btn-sm %s ajax', $grid::$btnSecondaryClass);
		$this->icon = 'pencil pencil-alt';

		$this->onControlAfterAdd[] = [$this, 'addControlsClasses'];
	}

	/**
	 * @return static
	 */
	public function setItemId(mixed $id): self
	{
		$this->itemID = $id;

		return $this;
	}

	public function getItemId(): mixed
	{
		return $this->itemID;
	}

	public function getPrimaryWhereColumn(): ?string
	{
		return $this->primaryWhereColumn;
	}

	public function renderButton(Row $row): Html
	{
		$a = Html::el('a')
			->href($this->grid->link('inlineEdit!', ['id' => $row->getId()]));

		$this->tryAddIcon($a, $this->getIcon(), $this->getText());

		$a->addText($this->text);

		if ($this->title !== null) {
			$a->setAttribute(
				'title',
				$this->grid->getTranslator()->translate($this->title)
			);
		}

		if ($this->class !== '') {
			$a->appendAttribute('class', $this->class);
		}

		$a->appendAttribute('class', 'datagrid-inline-edit-trigger');

		return $a;
	}

	/**
	 * Render row item detail button
	 */
	public function renderButtonAdd(): Html
	{
		$a = Html::el('a')
			->href($this->grid->link('showInlineAdd!'));

		$this->tryAddIcon($a, $this->getIcon(), $this->getText());

		$a->addText($this->text);

		if ($this->title !== null) {
			$a->setAttribute(
				'title',
				$this->grid->getTranslator()->translate($this->title)
			);
		}

		if ($this->class !== '') {
			$a->appendAttribute('class', $this->class);
		}

		return $a;
	}

	/**
	 * @return static
	 */
	public function setPositionTop(bool $positionTop = true): self
	{
		$this->positionTop = $positionTop;

		return $this;
	}

	public function isPositionTop(): bool
	{
		return $this->positionTop;
	}

	public function isPositionBottom(): bool
	{
		return !$this->positionTop;
	}

	public function addControlsClasses(Container $container): void
	{
		foreach ($container->getControls() as $key => $control) {
			switch ($key) {
				case 'submit':
					$control->setValidationScope([$container]);
					$control->setAttribute('class', 'btn btn-sm btn-primary');

					break;

				case 'cancel':
					$control->setValidationScope([]);
					$control->setAttribute('class', 'btn btn-sm btn-danger');

					break;

				default:
					if ($control->getControl()->getAttribute('class') === null) {
						if ($control instanceof TextBase) {
							$control->setHtmlAttribute('class', 'form-control form-control-sm');
						} else if ($control instanceof SelectBox) {
							$control->setHtmlAttribute('class', 'form-select form-select-sm');
						} else if ($control instanceof Checkbox || $control instanceof CheckboxList) {
							$control->getControlPrototype()->class = "form-check-input me-2";
						}
					}

					break;
			}
		}
	}

	/**
	 * @return static
	 */
	public function setShowNonEditingColumns(bool $show = true): self
	{
		$this->showNonEditingColumns = $show;

		return $this;
	}

	public function showNonEditingColumns(): bool
	{
		return $this->showNonEditingColumns;
	}

}
