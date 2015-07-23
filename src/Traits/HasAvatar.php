<?php

namespace GridPrinciples\FileApi\Traits;

use GridPrinciples\FileApi\Models\File;

trait HasAvatar {

    /**
     * What resolution is this avatar?  Always assume square avatars.
     * @var int
     */
    protected $avatarSize = 256;

    protected $avatarField = 'avatar_id';

    public function avatar()
    {
        return $this->belongsTo('GridPrinciples\FileApi\Models\File', $this->avatarField);
    }

    public function setAvatarAttribute($value)
    {
        $file = File::where('file_hash', $value)->first();

        if($file)
        {
            $this->avatar()->associate($file);
            $this->save();
        }
    }

    public function getAvatarSize()
    {
        return $this->avatarSize;
    }
}
