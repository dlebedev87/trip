<?php
/**
 * Created by PhpStorm.
 * User: Денис
 * Date: 28.06.2019
 * Time: 0:14
 */

namespace app\models;


use yii\base\Model;

class SearchForm extends Model
{
    public $airport_name;

    public function FormName(){
        return '';
    }

    public function rules()
    {
        return [
            [['airport_name'],'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'airport_name'=>'Аэропорт',
        ];
    }
}