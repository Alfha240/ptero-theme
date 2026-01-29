<?php

namespace Pterodactyl\Models;

class Announcement extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'announcements';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'content',
        'type',
        'visible_to',
        'active',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'active' => 'boolean',
    ];
}
