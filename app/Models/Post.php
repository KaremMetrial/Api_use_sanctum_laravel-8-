<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\SoftDeletes;

    class Post extends Model
    {
        use HasFactory, SoftDeletes;

        protected $fillable = [
            'title',
            'body',
            'cover_image',
            'pinned',
            'user_id'
        ];

        public function user()
        {
            $this->belongsTo(User::class, 'user_id', 'id');
        }

        public function tags()
        {
            $this->belongsToMany(Tag::class);
        }
    }
