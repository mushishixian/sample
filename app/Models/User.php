<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'activation_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));

        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function feed()
    {
        $userIds = \Auth::user()->followings->pluck('id');
        $userIds[] = \Auth::user()->id;

        return Status::whereIn('user_id', $userIds)
            ->with('user')
            ->orderBy('created_at', 'desc');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    public function follow($userIds)
    {
        //判断是不是数据,不是的话转换成数组
        if (!\is_array($userIds)) {
            $userIds = compact('userIds');
        }
        $this->followings()->sync($userIds, false);
    }

    public function unfollow($userIds)
    {
        if (!\is_array($userIds)) {
            $userIds = compact('userIds');
        }
        $this->followings()->detach($userIds);
    }

    public function isFollowing($userId)
    {
        return $this->followings->contains($userId);
    }
}
