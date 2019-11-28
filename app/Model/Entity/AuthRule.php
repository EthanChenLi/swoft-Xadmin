<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 节点表
 * Class AuthRule
 *
 * @since 2.0
 *
 * @Entity(table="auth_rule")
 */
class AuthRule extends Model
{
    /**
     * 
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * menu为菜单,file为权限节点
     *
     * @Column()
     *
     * @var string
     */
    private $type;

    /**
     * 父ID
     *
     * @Column()
     *
     * @var int
     */
    private $pid;

    /**
     * 规则名称
     *
     * @Column()
     *
     * @var string
     */
    private $title;

    /**
     * 图标
     *
     * @Column()
     *
     * @var string
     */
    private $icon;

    /**
     * 备注
     *
     * @Column()
     *
     * @var string
     */
    private $remark;

    /**
     * 创建时间
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var string|null
     */
    private $createdAt;

    /**
     * 更新时间
     *
     * @Column(name="updated_at", prop="updatedAt")
     *
     * @var string|null
     */
    private $updatedAt;

    /**
     * 权重
     *
     * @Column()
     *
     * @var int
     */
    private $weigh;

    /**
     * 状态
     *
     * @Column()
     *
     * @var int
     */
    private $status;

    /**
     * 
     *
     * @Column()
     *
     * @var string
     */
    private $route;


    /**
     * @param int $id
     *
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $type
     *
     * @return void
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param int $pid
     *
     * @return void
     */
    public function setPid(int $pid): void
    {
        $this->pid = $pid;
    }

    /**
     * @param string $title
     *
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param string $icon
     *
     * @return void
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    /**
     * @param string $remark
     *
     * @return void
     */
    public function setRemark(string $remark): void
    {
        $this->remark = $remark;
    }

    /**
     * @param string|null $createdAt
     *
     * @return void
     */
    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param string|null $updatedAt
     *
     * @return void
     */
    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param int $weigh
     *
     * @return void
     */
    public function setWeigh(int $weigh): void
    {
        $this->weigh = $weigh;
    }

    /**
     * @param int $status
     *
     * @return void
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @param string $route
     *
     * @return void
     */
    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getPid(): ?int
    {
        return $this->pid;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * @return int
     */
    public function getWeigh(): ?int
    {
        return $this->weigh;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

}
