<?php

namespace GridPrinciples\FileApi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class File extends Model {

    public function __construct()
    {
        parent::__construct();

        $this->setTable(config('files.table_name'));
    }

    protected $fillable = ['file_name', 'file_hash', 'file_size', 'content_type'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            $model->setUserFromAuth();
        });
    }

    /**
     * Relationship to the attaching models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * Relationship to the uploader.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the unassociated files.  These are usually files uploaded from forms for models
     * which do not yet have IDs; these files can't be associated on-upload.
     *
     * @param $query
     * @param string $type
     * @return mixed
     */
    public function scopeOrphans($query, $type = '')
    {
        if($type)
        {
            // Scope the results to a specific model
            $query->where('attachable_type', $type);
        }

        if(Auth::user())
        {
            // Limit the results to "my" uploads.
            $query->where('user_id', Auth::user()->id);
        }

        return $query->whereNull('attachable_id');
    }

    /**
     * Generate the local URL hosting this file.
     *
     * @param bool $size
     * @return string
     */
    public function getUrl($size = false)
    {
        return config('files.local_url') . '/' . $this->file_hash . ($size ? '/' . $size : '');
    }

    /**
     * Mutator shortcut for local URL generation.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->getUrl();
    }

    /**
     * If the user is logged in, associate them with this upload.
     */
    public function setUserFromAuth()
    {
        if(Auth::user())
        {
            $this->attributes['user_id'] = Auth::user()->id;
        }
    }

    /**
     * Determines whether this resource is an image.
     * 
     * @return bool
     */
    public function isImage()
    {
        return preg_match('/image\/jpeg|image\/gif|image\/png/', $this->getOriginal('content_type')) > 0;
    }
}
