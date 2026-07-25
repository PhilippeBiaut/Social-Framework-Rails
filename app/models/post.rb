class Post < ApplicationRecord
  belongs_to :user
  has_one_attached :image

  has_many :comments, -> { order(created_at: :asc) }, inverse_of: :post, dependent: :destroy
  has_many :likes, dependent: :destroy
  has_many :likers, through: :likes, source: :user

  validates :body, presence: true, length: { maximum: 1000 }
  validate :acceptable_image

  # `id` breaks ties so the order is total: the feed pages on an `id` cursor,
  # which only yields stable pages if the ordering is deterministic.
  scope :recent, -> { order(created_at: :desc, id: :desc) }

  # Everything `posts/_post` touches, so rendering a list of posts costs a fixed
  # number of queries instead of one per row.
  scope :with_card_data, -> { includes(:user, :likes, :comments, image_attachment: :blob) }

  # Reads the preloaded association when the caller eager-loaded `:likes`
  # (the feed does), instead of firing one EXISTS query per post.
  def liked_by?(user)
    return false unless user
    likes.any? { |like| like.user_id == user.id }
  end

  private

  def acceptable_image
    return unless image.attached?

    unless image.blob.content_type.in?(%w[image/png image/jpeg image/webp image/gif])
      errors.add(:image, "must be a PNG, JPEG, WEBP or GIF")
    end

    if image.blob.byte_size > 10.megabytes
      errors.add(:image, "is too large (max 10MB)")
    end
  end
end
