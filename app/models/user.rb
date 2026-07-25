class User < ApplicationRecord
  has_secure_password
  has_one_attached :avatar

  # Auth
  has_many :sessions, dependent: :destroy

  # Content
  has_many :posts, dependent: :destroy
  has_many :comments, dependent: :destroy
  has_many :likes, dependent: :destroy
  has_many :liked_posts, through: :likes, source: :post

  # Social graph (self-referential many-to-many)
  has_many :active_follows, class_name: "Follow", foreign_key: :follower_id, dependent: :destroy
  has_many :passive_follows, class_name: "Follow", foreign_key: :followed_id, dependent: :destroy
  has_many :following, through: :active_follows, source: :followed
  has_many :followers, through: :passive_follows, source: :follower

  normalizes :email_address, with: ->(e) { e.strip.downcase }
  normalizes :username, with: ->(u) { u.to_s.strip.downcase.presence }

  validates :email_address, presence: true, uniqueness: true,
                            format: { with: URI::MailTo::EMAIL_REGEXP }
  validates :username, presence: true, uniqueness: true,
                       length: { in: 3..30 },
                       format: { with: /\A[a-z0-9_]+\z/, message: "only lowercase letters, numbers and underscores" }
  validates :name, length: { maximum: 60 }
  validates :bio, length: { maximum: 280 }
  validate :acceptable_avatar

  # --- Social helpers ---------------------------------------------------------

  def follow(other_user)
    return if other_user == self
    active_follows.find_or_create_by(followed: other_user)
  end

  def unfollow(other_user)
    active_follows.where(followed: other_user).destroy_all
  end

  def following?(other_user)
    following.exists?(other_user.id)
  end

  # Posts from the people I follow, plus my own. Callers add ordering, eager
  # loading and pagination so the feed can be sliced without duplicating this.
  def feed
    Post.where(user_id: following_ids + [ id ])
  end

  # Use the username in generated URLs (/users/:username).
  def to_param
    username
  end

  def display_name
    name.presence || username
  end

  def initials
    display_name.to_s.split.map(&:first).first(2).join.upcase
  end

  private

  # Rails core has no `validates :avatar, content_type:` (that ships with the
  # active_storage_validations gem), so we emulate a small version here.
  def acceptable_avatar
    return unless avatar.attached?

    unless avatar.blob.content_type.in?(%w[image/png image/jpeg image/webp image/gif])
      errors.add(:avatar, "must be a PNG, JPEG, WEBP or GIF")
    end

    if avatar.blob.byte_size > 5.megabytes
      errors.add(:avatar, "is too large (max 5MB)")
    end
  end
end
