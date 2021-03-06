<?php

namespace App\Service;

use App\DAO\CommentDAO;
use DateTime;
use App\Model\Post;
use App\DAO\PostDAO;
use Ramsey\Uuid\Uuid;
use App\Service\Pagination\Paginator;

class PostManager
{
    private PostDAO $postDAO;
    private CommentDAO $commentDAO;
    private Paginator $paginator;

    public function __construct(PostDAO $postDAO, CommentDAO $commentDAO, Paginator $paginator)
    {
        $this->postDAO = $postDAO;
        $this->commentDAO = $commentDAO;
        $this->paginator = $paginator;
    }

    /**
     * Creates and saves the Post in database.
     */
    public function manageCreatePost(Post $post): Post
    {
        if ($post->getIsPublished()) {
            $this->addSlug($post);
        }

        $dateTime = new DateTime('now');
        $post->setUuid(Uuid::uuid4())
            ->setCreatedAt($dateTime)
            ->setUpdatedAt($dateTime);
        $this->postDAO->add($post);

        return $post;
    }

    /**
     * Creates and saves the Post in database.
     */
    public function manageEditPost(Post $post): Post
    {
        if ($post->getIsPublished() && null === $post->getSlug()) {
            $this->addSlug($post);
        }

        $post->setUpdatedAt(new DateTime('now'));
        $this->postDAO->updatePost($post);

        return $post;
    }

    /**
     * Returns Paginator.
     */
    public function getPaginationForAllPosts(string $searchTerms = null, int $pageNumber): Paginator
    {
        // sets the query for the pagination
        $this->postDAO->setAllPostsQuery($searchTerms);

        // creates the pagination for the template
        return $this->paginator->paginate(
            $this->postDAO,
            $pageNumber < 1 ? 1 : $pageNumber,
            15
        );
    }

    /**
     * Returns Paginator.
     */
    public function getPaginationForIsPublishedAndSearchTerms(string $searchTerms = null, int $pageNumber): Paginator
    {
        // sets the query for the pagination
        $this->postDAO->setIsPublishedAndSearchQuery($searchTerms);

        // creates the pagination for the template
        return $this->paginator->paginate(
            $this->postDAO,
            $pageNumber < 1 ? 1 : $pageNumber,
            5
        );
    }

    /**
     * Returns Paginator
     */
    public function getPaginationForDraftPosts(int $pageNumber): Paginator
    {
        // sets the query for the pagination
        $this->postDAO->setNeverPublishedQuery();

        // creates the pagination for the template
        return $this->paginator->paginate(
            $this->postDAO,
            $pageNumber < 1 ? 1 : $pageNumber,
            15
        );
    }

    /**
     * @return null|Post
     */
    public function getOneBySlug(string $slug)
    {
        $post = $this->postDAO->getOneBySlug($slug);
        if (null !== $post) {
            $comments = $this->commentDAO->getValidatedCommentsByPostId($post->getId());
            if (null !== $comments) {
                $post->setComments($comments);
            }
        }

        return $post;
    }

    /**
     * @return null|object|Post
     */
    public function getPostByUuid(string $uuid)
    {
        return $this->postDAO->getOneByUuid($uuid);
    }

    /**
     * Adds a slug to the post.
     */
    public function addSlug(Post $post): Post
    {
        return $post->setSlug($this->slugify($post->getTitle()));
    }

    /**
     * Slugify the title.
     */
    public function slugify(string $title): string
    {
        $slug = mb_strtolower(
            (string) preg_replace(
                array('/[^a-zA-Z0-9 \'-]/', '/[ -\']+/', '/^-|-$/'),
                array('', '-', ''),
                $this->removeAccent(trim($title))
            )
        );

        // retrieves identical slugs from database
        $posts = (array) $this->postDAO->getPostsBySlug($slug);

        // If not already exists, returns the slug without index
        if (count($posts) === 0) {
            return $slug;
        }

        $slugs = [];

        foreach ($posts as $post) {
            $slugs[] = $post->getSlug();
        }

        // get only the index character of the slugs and sort them in ascending
        $slugs = array_map(function ($element) use ($title) {
            return substr($element, strlen($title) + 1);
        }, $slugs);

        asort($slugs, SORT_NUMERIC);

        // attach the last index + 1 to the slug
        return $slug . '-' . ((int) $slugs[array_key_last($slugs)] + 1);
    }

    public function removeAccent(string $string): string
    {
        $search = ['À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
            'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã',
            'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
            'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ',
            'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę',
            'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī',
            'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ',
            'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ',
            'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť',
            'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ',
            'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ',
            'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ'];

        $replace = ['A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O',
            'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c',
            'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u',
            'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D',
            'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g',
            'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K',
            'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o',
            'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S',
            's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W',
            'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i',
            'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o'];

        return str_replace($search, $replace, $string);
    }
}
