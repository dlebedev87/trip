<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "airport_name".
 *
 * @property int $id
 * @property int $airport_id
 * @property int $language_id
 * @property string $value
 */
class AirportName extends \yii\db\ActiveRecord
{
    const LANGUAGE_ID_RUS = 137;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'airport_name';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_airport_name');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['airport_id', 'value'], 'required'],
            [['airport_id', 'language_id'], 'integer'],
            [['value'], 'string', 'max' => 255],
            [['airport_id', 'language_id'], 'unique', 'targetAttribute' => ['airport_id', 'language_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'airport_id' => 'Airport ID',
            'language_id' => 'Language ID',
            'value' => 'Value',
        ];
    }

    public function getFlightSegments()
    {
        return $this->hasOne(FlightSegment::className(), ['depAirportId' => 'airport_id']);
    }

}
