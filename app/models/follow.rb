class Follow < ApplicationRecord
  belongs_to :follower, class_name: "User"
  belongs_to :followed, class_name: "User"

  validates :follower_id, uniqueness: { scope: :followed_id }
  validate :not_self_follow

  private

  def not_self_follow
    errors.add(:followed_id, "can't follow yourself") if follower_id == followed_id
  end
end
