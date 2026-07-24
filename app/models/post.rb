class Post < ApplicationRecord
  belongs_to :user
  has_one_attached :image

  has_many :comments, -> { order(created_at: :asc) }, dependent: :destroy
  has_many :likes, dependent: :destroy
  has_many :likers, through: :likes, source: :user

  validates :body, presence: true, length: { maximum: 1000 }
  validate :acceptable_image

  scope :recent, -> { order(created_at: :desc) }

  def liked_by?(user)
    return false unless user
    likes.exists?(user_id: user.id)
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
