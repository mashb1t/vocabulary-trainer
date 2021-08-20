<?php

namespace App\Http\Livewire;

use App\Models\Quiz;
use Illuminate\Database\Eloquent\Model;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;

class QuizzesTable extends AbstractDataTable
{
    public $model = Quiz::class;
    public ?Model $editing;

    protected $route = 'quiz';

    public function rules()
    {
        return [
            'editing.title' => 'required|min:3',
            'editing.description' => 'required|min:3',
        ];
    }

    public function builder()
    {
        $builder = $this->model::query()
            ->join('questions', 'quizzes.id', 'questions.quiz_id')
//            ->leftJoin('slots', 'questions.slot_id', 'slots.id')
            ->whereOwner(auth()->id())
            ->groupBy('quizzes.id');

        return $builder;
    }

    public function columns()
    {
        return [
            NumberColumn::name('id')->label('ID'),
            Column::name('title'),
            Column::name('description')->truncate(50),
            NumberColumn::name('questions.id')->label(__('# Questions'))->width(150),

            Column::callback(['id', 'title'], function ($id, $name) {
                return view('datatables.table-actions', [
                    'id' => $id,
                    'name' => $name,
                    'route' => $this->route,
                    'actions' => [
                        'view' => false,
                        'edit' => true,
                        'delete' => true,
                    ],
                ]);
            })->label('Actions'),
        ];
    }
}
