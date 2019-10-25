<?php

namespace MakairaConnect\Repositories;

use Doctrine\ORM\EntityRepository;
use MakairaConnect\Models\MakRevision as MakRevisionModel;

/**
 * MakRevisionRepository
 */
class MakRevisionRepository extends EntityRepository {

  /**
   * @param $type
   * @param $objectId
   * @throws \Doctrine\ORM\OptimisticLockException
   */
  public function addRevision($type, $objectId) {
    $data = ['type' => $type, 'id' => $objectId];

    /** @var MakRevisionModel $revision */
    $revision = $this->findOneBy($data);

    if($revision) {
      $this->_em->remove($revision);
      $this->_em->flush();
    }

    $revision = new MakRevisionModel();
    $revision->fromArray($data);
    $this->_em->persist($revision);
    $this->_em->flush();
  }

  /**
   * @param $lastRev
   * @param int $count
   *
   * @return \MakairaConnect\Models\MakRevision[]
   */
  public function getRevisions($lastRev, $count = 50) {
    $query = $this->createQueryBuilder('revisions')
      ->select()
      ->where('revisions.sequence > :lastRev')
      ->setParameter('lastRev', $lastRev)
      ->setMaxResults($count)
      ->orderBy('revisions.sequence', 'ASC')
      ->getQuery();

    return $query->getResult();
  }
}