<?php
namespace PHPDish\Bundle\PostBundle\Service;

use Carbon\Carbon;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPDish\Bundle\CoreBundle\Service\PaginatorTrait;
use PHPDish\Bundle\PostBundle\Entity\Comment;
use PHPDish\Bundle\PostBundle\Model\CommentInterface;
use PHPDish\Bundle\PostBundle\Model\PostInterface;
use PHPDish\Bundle\UserBundle\Model\UserInterface;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use PHPDish\Component\Mention\MentionParserInterface;

class CommentManager implements CommentManagerInterface
{
    use PaginatorTrait;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var MarkdownParserInterface
     */
    protected $markdownParser;

    /**
     * @var MentionParserInterface
     */
    protected $mentionParser;

    /**
     * @var EntityRepository
     */
    protected $commentRepository;

    public function __construct(EntityManagerInterface $entityManager, MarkdownParserInterface $markdownParser, MentionParserInterface $mentionParser)
    {
        $this->entityManager  = $entityManager;
        $this->commentRepository = $entityManager->getRepository('PHPDishPostBundle:Comment');
        $this->markdownParser = $markdownParser;
        $this->mentionParser = $mentionParser;
    }

    /**
     * {@inheritdoc}
     */
    public function createComment(PostInterface $post, UserInterface $user)
    {
        $comment = new Comment();
        $comment->setPost($post)->setUser($user)
            ->setCreatedAt(Carbon::now());
        $post->increaseCommentCount();
        return $comment;
    }

    /**
     * {@inheritdoc}
     */
    public function saveComment(CommentInterface $comment)
    {
        $body = $this->markdownParser->transformMarkdown($comment->getOriginalBody());
        $parsedBody = $this->mentionParser->parse($body)->getParsedBody();
        $comment->setUpdatedAt(Carbon::now())
            ->setBody($parsedBody);
        $this->entityManager->persist($comment);
        $this->entityManager->flush();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function findComments(Criteria $criteria, $page = 1, $limit = null)
    {
        $query = $this->commentRepository->createQueryBuilder('c')
            ->addCriteria($criteria)
            ->getQuery();
        return $this->createPaginator($query, $page, $limit);
    }
}