<?php

namespace PHPDish\Bundle\PostBundle\Service;

use Doctrine\Common\Collections\Criteria;
use Pagerfanta\Pagerfanta;
use PHPDish\Bundle\PostBundle\Model\CommentInterface;
use PHPDish\Bundle\PostBundle\Model\PostInterface;
use PHPDish\Bundle\UserBundle\Model\UserInterface;

interface CommentManagerInterface
{
    /**
     * 根据id获取评论.
     *
     * @param int $id
     *
     * @return CommentInterface
     */
    public function findCommentById($id);

    /**
     * 创建评论.
     *
     * @param PostInterface $post
     * @param UserInterface $user
     *
     * @return CommentInterface
     */
    public function createComment(PostInterface $post, UserInterface $user);

    /**
     * 保存comment.
     *
     * @param CommentInterface $comment
     *
     * @return bool
     */
    public function saveComment(CommentInterface $comment);

    /**
     * 封禁comment.
     *
     * @param CommentInterface $comment
     */
    public function blockComment(CommentInterface $comment);

    /**
     * 获取评论.
     *
     * @param Criteria $criteria
     * @param int      $page
     * @param null     $limit
     *
     * @return Pagerfanta
     */
    public function findComments(Criteria $criteria, $page = 1, $limit = null);
}
