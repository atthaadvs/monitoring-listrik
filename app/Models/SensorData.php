<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    protected $table = 'sensor_data';

    protected $fillable = [
        'temperature',
        'humidity',
        'power_status',
        'voltage',
        'location',
        'recorded_at',
        'current',
        'power'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'power_status' => 'boolean',
    ];

    public function getTemperatureStatusAttribute()
    {
        if ($this->temperature >= 18 && $this->temperature <= 27) {
            return 'normal';
        } elseif ($this->temperature > 27) {
            return 'high';
        } else {
            return 'low';
        }
    }

    public function getHumidityStatusAttribute()
    {
        if ($this->humidity >= 40 && $this->humidity <= 60) {
            return 'normal';
        } elseif ($this->humidity > 60) {
            return 'high';
        } else {
            return 'low';
        }
    }

    public function getStatusColorAttribute()
    {
        $tempStatus = $this->temperature_status;
        $humidityStatus = $this->humidity_status;
        
        if ($tempStatus === 'normal' && $humidityStatus === 'normal') {
            return 'green';
        } elseif ($tempStatus === 'high' || $humidityStatus === 'high') {
            return 'red';
        } else {
            return 'yellow';
        }
    }
}
