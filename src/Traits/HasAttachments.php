<?php

namespace GridPrinciples\FileApi\Traits;

trait HasAttachments {

    /**
     * The relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function files()
    {
        return $this->morphMany('GridPrinciples\FileApi\Models\File', 'attachable');
    }
}
