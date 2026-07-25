# Idempotent demo data for the Social Framework.
# Run with: bin/rails db:seed   (or bin/rails db:reset to rebuild)

puts "Seeding users…"

PEOPLE = [
  { username: "ada",     name: "Ada Lovelace",     bio: "Writing the first algorithm, one loop at a time. 💻" },
  { username: "grace",   name: "Grace Hopper",     bio: "Debugging since before it was cool. 🦟" },
  { username: "linus",   name: "Linus T.",         bio: "Just for fun. Kernel enthusiast." },
  { username: "dhh",     name: "David H.",          bio: "Convention over configuration. Rails forever. 🚀" },
  { username: "yukihiro", name: "Yukihiro M.",     bio: "Optimizing for developer happiness." },
  { username: "sam",     name: "Sam Stephenson",   bio: "Hotwire, Turbo, Stimulus. HTML over the wire." }
]

users = PEOPLE.map do |attrs|
  User.find_or_create_by!(username: attrs[:username]) do |u|
    u.email_address = "#{attrs[:username]}@example.com"
    u.name = attrs[:name]
    u.bio = attrs[:bio]
    u.password = "password"
    u.password_confirmation = "password"
  end
end

ada, grace, linus, dhh, yukihiro, sam = users

puts "Building the social graph…"
# Everyone follows a couple of others.
[
  [ ada, grace ], [ ada, dhh ], [ ada, sam ],
  [ grace, ada ], [ grace, linus ],
  [ linus, yukihiro ], [ linus, dhh ],
  [ dhh, sam ], [ dhh, yukihiro ], [ dhh, ada ],
  [ yukihiro, dhh ], [ yukihiro, sam ],
  [ sam, dhh ], [ sam, ada ], [ sam, grace ]
].each { |follower, followed| follower.follow(followed) }

puts "Writing posts…"
POSTS = [
  [ ada,      "Hello world 👋 Excited to join this little corner of the internet." ],
  [ grace,    "Reminder: it's easier to ask forgiveness than permission. Ship it." ],
  [ dhh,      "Just refactored a controller down to 6 lines. Convention over configuration is undefeated." ],
  [ sam,      "Turbo Frames + Stimulus = no-build SPA feel with plain HTML. Try it, you'll love it." ],
  [ yukihiro, "A programming language should feel natural. Ruby was designed for humans first." ],
  [ linus,    "Talk is cheap. Show me the code." ],
  [ ada,      "Spent the afternoon reading about Turbo Streams. Real-time updates without a single line of custom JS 🤯" ],
  [ dhh,      "Dark mode toggle in 20 lines of Stimulus. The web platform is good, actually." ],
  [ grace,    "The most dangerous phrase is 'we've always done it this way'." ],
  [ sam,      "Small controllers, small partials, small everything. Composition scales." ]
]

# Insert oldest first so ids grow with time, the way real traffic writes them.
# The feed pages on an id cursor, so backdating in reverse would make the demo
# data order by id disagree with order by created_at.
posts = POSTS.reverse.map.with_index do |(user, body), i|
  post = user.posts.find_or_create_by!(body: body)
  post.update_column(:created_at, (POSTS.size - 1 - i).hours.ago)
  post
end.reverse

puts "Adding likes & comments…"
# Deterministic spread of likes (no rand) so re-seeding is a no-op.
posts.each_with_index do |post, i|
  users.rotate(i).first(1 + (i % 4)).each { |u| u.likes.find_or_create_by!(post: post) }
end

[
  [ grace, 0, "Welcome aboard! 🎉" ],
  [ dhh,   0, "Great to have you here." ],
  [ ada,   3, "This is exactly what I was looking for. Thanks Sam!" ],
  [ linus, 2, "Six lines? Rookie numbers." ]
].each do |user, index, body|
  Comment.find_or_create_by!(user: user, post: posts[index], body: body)
end

puts "Done! #{User.count} users, #{Post.count} posts, #{Comment.count} comments, #{Like.count} likes."
puts "Sign in with any of: #{PEOPLE.map { |p| p[:username] + '@example.com' }.join(', ')} (password: 'password')"
