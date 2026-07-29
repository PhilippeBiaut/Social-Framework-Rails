class TightenDomainNullConstraints < ActiveRecord::Migration[8.1]
  # These were enforced by model validations only, so anything bypassing them
  # (update_column, raw SQL, a fixture) could persist a row the app can't render.
  def change
    up_only do
      execute "UPDATE users SET username = 'user_' || id WHERE username IS NULL OR username = ''"
      execute "UPDATE posts SET body = '' WHERE body IS NULL"
      execute "UPDATE comments SET body = '' WHERE body IS NULL"
    end

    change_column_null :users, :username, false
    change_column_null :posts, :body, false
    change_column_null :comments, :body, false
  end
end
