module ApplicationHelper
  # Renders a user's avatar (uploaded image or gradient initials fallback).
  def avatar_for(user, size: "size-10", text: "text-sm", link: true, ring: true)
    ring_class = ring ? "avatar-ring" : ""

    inner =
      if user.avatar.attached?
        image_tag user.avatar, alt: user.display_name,
                  class: "#{size} #{ring_class} rounded-full object-cover"
      else
        content_tag :span, user.initials,
                    class: "#{size} #{text} #{ring_class} inline-flex items-center justify-center rounded-full " \
                           "bg-gradient-to-br from-indigo-500 to-fuchsia-500 font-semibold text-white select-none"
      end

    link ? link_to(inner, user_path(user), class: "shrink-0", data: { turbo_frame: "_top" }) : inner
  end

  def timestamp(time)
    content_tag :time, relative_time(time),
                datetime: time.iso8601, title: time.strftime("%B %-d, %Y at %H:%M"),
                class: "text-xs text-gray-400"
  end

  # Deliberately hand-rolled rather than `time_ago_in_words`: it rounds ("about
  # 7 months") where every other stack truncates, so the two ports would print
  # different strings for the same instant. Floor every unit, no fuzzy prefix.
  def relative_time(time)
    seconds = (Time.current - time).to_i
    return "just now" if seconds < 60

    minutes = seconds / 60
    return pluralize_unit(minutes, "minute") if minutes < 60

    hours = minutes / 60
    return pluralize_unit(hours, "hour") if hours < 24

    days = hours / 24
    return pluralize_unit(days, "day") if days < 30

    months = days / 30
    return pluralize_unit(months, "month") if months < 12

    pluralize_unit(days / 365, "year")
  end

  # Flowbite-flavoured toast colours per flash type.
  def flash_variant(type)
    case type.to_s
    when "notice", "success"
      { icon: "✓", ring: "text-green-500 bg-green-100 dark:bg-green-800 dark:text-green-200" }
    when "alert", "error"
      { icon: "!", ring: "text-red-500 bg-red-100 dark:bg-red-800 dark:text-red-200" }
    else
      { icon: "i", ring: "text-indigo-500 bg-indigo-100 dark:bg-indigo-800 dark:text-indigo-200" }
    end
  end

  private

  def pluralize_unit(count, unit)
    "#{count} #{unit}#{"s" unless count == 1} ago"
  end
end
