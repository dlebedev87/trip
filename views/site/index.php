<?php

/* @var $this yii\web\View */


use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\jui\AutoComplete;
use yii\grid\GridView;

$this->title = 'Trip';

?>
<div class="site-index">
    <div class="trip-search">
        <div class="log-search">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
            ]); ?>

            <?= $form->field($model, 'airport_name')->label('Аэропорт:')->widget(AutoComplete::class,[
                'clientOptions' => [
                    'source' => $listdata,
                    'minLength'=>'3',
                    'autoFill'=>true,
                ],
            ]); ?>
            <div class="form-group">
                <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('Сброс', ['class' => 'btn btn-outline-secondary','onclick'=>"location.href='/'"]) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?
    if($dataProvider){
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
            ]
        ]);
    }
    ?>
</div>

