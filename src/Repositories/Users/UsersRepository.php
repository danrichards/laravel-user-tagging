<?php

namespace Dan\Tagging\Repositories\Users;

use Torann\LaravelRepository\Repositories\AbstractRepository;
use Dan\Tagging\Traits\Util as TaggingUtility;
use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * Class UsersRepository
 */
class UsersRepository extends AbstractRepository implements UsersInterface
{
    
    use TaggingUtility;

    /**
     * Specify Model class name
     *
     * @return string
     */
    protected $model = User::class;

    /**
     * @param \App\User|Model|int $user
     * @return array[int]
     */
    public function tagsIdsFor($user)
    {
        /** @var \Dan\Tagging\Repositories\Tags\TagsRepository $tags */
        $tags = app($this->tagUtil()->tagsRepositoryInterface());
        return $tags->findAllBy('slug', $this->taggedColFor($user, 'tag_slug'))
            ->pluck('id')->all();
    }

    /**
     * @param \App\User|Model $user
     * @return \Illuminate\Database\Eloquent\Collection [Tag]
     */
    public function tagsFor($user)
    {
        /** @var \Dan\Tagging\Repositories\Tags\TagsRepository $tags */
        $tags = app($this->tagUtil()->tagsRepositoryInterface());
        return $tags->findAllBy('id', $this->tagsIdsFor($user));
    }

    /**
     * @param \App\User|Model|int $user
     * @param string $taggedCol
     * @return array [int|string]
     */
    public function taggedColFor($user, $taggedCol = 'id')
    {
        $userModel = $this->tagUtil()->userModel();
        $userId = $user instanceof $userModel ? $user->getKey() : $user;

        $taggedModel = $this->tagUtil()->taggedModelString();
        return $taggedModel::select(sprintf('tagging_tagged.%s AS %s', $taggedCol, $taggedCol))
            ->join('tagging_tagged_user AS tu', 'tu.tagged_id', '=', 'tagging_tagged.id')
            ->where('tu.user_id', '=', $userId)
            ->pluck($taggedCol)
            ->unique()
            ->all();
    }

    /**
     * @param \App\User|Model $user
     * @return array[int]
     */
    public function taggedIdsFor($user)
    {
        $userModel = $this->tagUtil()->userModel();
        $userId = $user instanceof $userModel ? $user->getKey() : $user;
        
        $taggedUserModel = $this->tagUtil()->taggedUserModel();
		return $taggedUserModel::select('tagged_id')
			->where('user_id', $userId)
			->pluck('tagged_id')
			->all();
    }

    /**
     * @param \App\User|Model $user
     * @return \Illuminate\Database\Eloquent\Collection [Tagged]
     */
    public function taggedFor($user)
    {
        $taggedIds = $this->taggedIdsFor($user);
        /** @var \Dan\Tagging\Repositories\Tagged\TaggedRepository $tagged */
        $tagged = app($this->tagUtil()->taggedRepositoryInterface());
        return $tagged->findAllBy('id', $taggedIds);
    }

    /**
     * Models that have been tagged by the user.
     *
     * @param \App\User|Model $user
     * @return \Illuminate\Database\Eloquent\Collection [Model]
     */
    public function taggedTaggablesFor($user)
    {
        $tagged = $this->taggedFor($user);

        $taggables = $tagged->map(function($item) {
            /** @var \Dan\Tagging\Models\Tagged $item */
            return $item->taggable()->first();
        });

        return $taggables->unique();
    }
}