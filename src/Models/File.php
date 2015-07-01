<?php

namespace GridPrinciples\FileApi\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model {
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
}
