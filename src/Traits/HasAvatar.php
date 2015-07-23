<?php

namespace GridPrinciples\FileApi\Traits;


trait HasAvatar {

    /**
     * What resolution is this avatar?  Always assume square avatars.
     * @var int
     */
    protected $size = 256;

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
        }
    }
}
