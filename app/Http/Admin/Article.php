<?php

namespace App\Http\Admin;

use AdminColumn;
use AdminColumnFilter;
use AdminDisplay;
use AdminForm;
use AdminFormElement;
use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Form\Buttons\Cancel;
use SleepingOwl\Admin\Form\Buttons\Save;
use SleepingOwl\Admin\Form\Buttons\SaveAndClose;
use SleepingOwl\Admin\Form\Buttons\SaveAndCreate;
use SleepingOwl\Admin\Section;
use Illuminate\Support\Str;


/**
 * Class Article
 *
 * @property \App\Models\Article $model
 *
 * @see https://sleepingowladmin.ru/#/ru/model_configuration_section
 */
class Article extends Section implements Initializable
{

    protected $model;
    /**
     * @var bool
     */
    protected $checkAccess = false;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $alias;

    /**
     * Initialize class.
     */
    public function initialize()
    {
        $this->addToNavigation()->setPriority(100)->setIcon('fa fa-lightbulb-o');

//         $this->creating(function ($config, \Illuminate\Database\Eloquent\Model $model) {
//             dd($config,request());
//         });
//
        $this->updating(function ($config) {
            $request = request();
            $requestEntries = request()->all();

            foreach ($requestEntries as $field=>$value)   {
                $arr=explode('->', $field);
                if(count($arr) > 1){
                    $singular = Str::singular($arr[0]);
                    $request->request->add([$singular.'->'.$arr[1] => $value]);
                    $request->request->remove($field);
                }
            }
//            dd(request());
        });
    }

    /**
     * @param array $payload
     *
     * @return DisplayInterface
     */
    public function onDisplay($payload = [])
    {
        $columns = [
            AdminColumn::text('id', '#')->setWidth('50px')->setHtmlAttribute('class', 'text-center'),
            AdminColumn::link('title', 'Title', 'created_at')
                ->setSearchCallback(function ($column, $query, $search) {
                    return $query
                        ->orWhere('title', 'like', '%' . $search . '%')
                        ->orWhere('created_at', 'like', '%' . $search . '%');
                })
                ->setOrderable(function ($query, $direction) {
                    $query->orderBy('created_at', $direction);
                }),
            AdminColumn::boolean('public', 'On'),
            AdminColumn::text('created_at', 'Created / updated', 'updated_at')
                ->setWidth('160px')
                ->setOrderable(function ($query, $direction) {
                    $query->orderBy('updated_at', $direction);
                })
                ->setSearchable(false),
        ];

        $display = AdminDisplay::datatables()
            ->setName('firstdatatables')
            ->setOrder([[0, 'asc']])
            ->setDisplaySearch(true)
            ->paginate(25)
            ->setColumns($columns)
            ->setHtmlAttribute('class', 'table-primary table-hover th-center');

        $display->setColumnFilters([
            AdminColumnFilter::select()
                ->setModelForOptions(\App\Models\Article::class, 'title')
                ->setLoadOptionsQueryPreparer(function ($element, $query) {
                    return $query;
                })
                ->setDisplay('name')
                ->setColumnName('title')
                ->setPlaceholder('All names'),
        ]);
        $display->getColumnFilters()->setPlacement('card.heading');

        return $display;
    }

    /**
     * @param int|null $id
     * @param array $payload
     *
     * @return FormInterface
     */
    public function onEdit($id = null, $payload = [])
    {
        $data = $this->model_value?->translations;

        $tabs = AdminDisplay::tabbed();

        $tabs->setTabs(function ($id) use ($data) {

            $tabs = [];


            foreach (config('app.locales') as $lang => $langTitle) {


                $tabs[] = AdminDisplay::tab(
                    AdminForm::elements([

                        AdminFormElement::columns()
                            ->addColumn([
                                AdminFormElement::text('titles->' . $lang, 'Title')
                                    ->setValue('data')
                                    ->required(),
                            ], 'col-xs-12 col-sm-6 ')
                            ->addColumn([
                                AdminFormElement::text('slugs->' . $lang, 'Slug'),
                                AdminFormElement::html('Will be auto-generated from the title on the first creation')
                            ], 'col-xs-12 col-sm-6 '),

                        AdminFormElement::textarea('excerpts->' . $lang, 'Excerpt')->required(),
                        AdminFormElement::wysiwyg('bodies->' . $lang, 'Body')->required(),
                    ])
                )->setLabel($langTitle);

            }

            return $tabs;
        });


        $form = AdminForm::card()->addHeader([
            $tabs
        ])->addBody([

            AdminFormElement::image('image', 'Main Image'),
            AdminFormElement::checkbox('public', 'Public'),
        ]);


        $form->getButtons()->setButtons([
            'save' => new Save(),
            'save_and_close' => new SaveAndClose(),
            'save_and_create' => new SaveAndCreate(),
            'cancel' => (new Cancel()),
        ]);

        return $form;
    }

    /**
     * @return FormInterface
     */
    public function onCreate($payload = [])
    {
        return $this->onEdit(null, $payload);
    }

    /**
     * @return bool
     */
    public function isDeletable(Model $model)
    {
        return true;
    }

    /**
     * @return void
     */
    public function onRestore($id)
    {
        // remove if unused
    }
}
