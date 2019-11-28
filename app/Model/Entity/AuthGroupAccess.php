<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 权限分组表
 * Class AuthGroupAccess
 *
 * @since 2.0
 *
 * @Entity(table="auth_group_access")
 */
class AuthGroupAccess extends Model
{
    /**
     * 会员ID
     * @Id(incrementing=false)
     * @Column()
     *
     * @var int
     */
    private $uid;

    /**
     * 级别ID
     *
     * @Column(name="group_id", prop="groupId")
     *
     * @var int
     */
    private $groupId;


    /**
     * @param int $uid
     *
     * @return void
     */
    public function setUid(int $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @param int $groupId
     *
     * @return void
     */
    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    /**
     * @return int
     */
    public function getUid(): ?int
    {
        return $this->uid;
    }

    /**
     * @return int
     */
    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

}
