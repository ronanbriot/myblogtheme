<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Comments extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'partials.comments',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            'title' => $this->title(),
            'responses' => $this->responses(),
            'previous' => $this->previous(),
            'next' => $this->next(),
            'paginated' => $this->paginated(),
            'closed' => $this->closed(),
            'commentFormArgs' => $this->commentFormArgs()
        ];
    }

    /**
     * The comment title.
     *
     * @return string
     */
    public function title()
    {
        return sprintf(
            /* translators: %1$s is replaced with the number of comments and %2$s with the post title */
            _nx('%1$s response to &ldquo;%2$s&rdquo;', '%1$s responses to &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'sage'),
            get_comments_number() === 1 ? _x('One', 'comments title', 'sage') : number_format_i18n(get_comments_number()),
            get_the_title()
        );
    }

    /**
     * Retrieve the comments.
     *
     * @return string
     */
    public function responses()
    {
        if (! have_comments()) {
            return;
        }

        return wp_list_comments([
            'style' => 'ol',
            'short_ping' => true,
            'echo' => false,
        ]);
    }

    /**
     * The previous comments link.
     *
     * @return string
     */
    public function previous()
    {
        if (! get_previous_comments_link()) {
            return;
        }

        return get_previous_comments_link(
            __('&larr; Older comments', 'sage')
        );
    }

    /**
     * The next comments link.
     *
     * @return string
     */
    public function next()
    {
        if (! get_next_comments_link()) {
            return;
        }

        return get_next_comments_link(
            __('Newer comments &rarr;', 'sage')
        );
    }

    /**
     * Determine if the comments are paginated.
     *
     * @return bool
     */
    public function paginated()
    {
        return get_comment_pages_count() > 1 && get_option('page_comments');
    }

    /**
     * Determine if the comments are closed.
     *
     * @return bool
     */
    public function closed()
    {
        return ! comments_open() && get_comments_number() != '0' && post_type_supports(get_post_type(), 'comments');
    }

    public function commentFormArgs(): array
    {
        global $post;

        $post_id       = $post->ID;
        $user          = wp_get_current_user();
        $user_identity = $user->exists() ? $user->display_name : '';
        $required_indicator = ' ' . wp_required_field_indicator();
        $required_text      = ' ' . wp_required_field_message();
        $required_attribute = ' required="required"';

        $loggedInAsHtmlInner = sprintf(
            /* translators: 1: User name, 2: Edit user link, 3: Logout URL. */
            __('Logged in as %s.<br />', 'sage'),
            $user_identity
        );
        $loggedInAsHtmlInner .= sprintf(
            __('<a href="%s">Edit your profile</a>.<br />', 'sage'),
            get_edit_user_link()
        );
        $loggedInAsHtmlInner .= sprintf(
            __('<a href="%s">Log out ?</a><br />', 'sage'),
            /** This filter is documented in wp-includes/link-template.php */
            wp_logout_url(apply_filters('the_permalink', get_permalink($post_id), $post_id))
        );

        $loggedInAsHtml = sprintf(
            '<p class="logged-in-as float-right text-right">%s</p>',
            $loggedInAsHtmlInner
        );

        $commentField = sprintf(
            '<p class="comment-form-comment">%s<br />%s %s</p>',
            $required_text,
            sprintf(
                '<label for="comment">%s%s</label>',
                _x('Comment', 'noun'),
                $required_indicator
            ),
            '<textarea id="comment" class="textarea max-w-sm text-base-content" name="comment" cols="45" rows="8" maxlength="65525"' . $required_attribute . '></textarea>'
        );

        $commentFormArgs = array(
            'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title text-primary/90 text-2xl">',
            'logged_in_as' => $loggedInAsHtml,
            'comment_field' => $commentField,
            'submit_field' => '<p class="form-submit py-3">%1$s %2$s</p>',
            'submit_button' => '<input name="%1$s" type="submit" id="%2$s" class="%3$s btn btn-primary" value="%4$s" />'
        );
        return $commentFormArgs;
    }
}
