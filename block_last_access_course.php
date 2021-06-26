<?php

/**
 * Contains the class for the Last Access Course block.
 *
 * @package    block_last_access_course
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author CaptKraken
 */

//settings
define('BLOCK_LAST_ACCESS_COURSE_BTN_TEXT_COLOR', "#030303");
define('BLOCK_LAST_ACCESS_COURSE_BTN_BACKGROUND_COLOR', "#f1f1f1");
define('BLOCK_LAST_ACCESS_COURSE_THUMB_BACKGROUND_COLOR', "#c3c3c3");
define('BLOCK_LAST_ACCESS_COURSE_THUMB_TEXT_COLOR', "#f1f1f1");
define('BLOCK_LAST_ACCESS_COURSE_COURSE_NUMBER', 3);
define('BLOCK_LAST_ACCESS_COURSE_SHOW_TIME_ELAPSED', 1);


//if not logged in, dont display block. had to do this because it would give me an error if not logged in
if (!isloggedin()) {
    return;
} else {

    class block_last_access_course extends block_base
    {
        function init()
        {
            $this->title = get_string('pluginname', 'block_last_access_course');
        }

        /**
         * generates an acronym with the course name
         * 
         * @param string  $course_name The course name to be acronymize
         * @return string 
         * 
         * @ Michael Berkowski from: https://stackoverflow.com/questions/9706429/get-the-first-letter-of-each-word-in-a-string
         */
        function get_course_acronym($course_name)
        {
            $words = explode(" ", $course_name);
            $acronym = "";

            foreach ($words as $w) {
                $acronym .= $w[0];
            }
            return $acronym;
        }

        /**
         * 
         * gets course's image
         * 
         * @param object $course The course object
         * @return string|false Returns course image url or false if no course image. 
         * 
         * @ Florian Metzger-Noel from: https://stackoverflow.com/questions/61818875/how-to-get-course-image-from-moodle-api
         * 
         * You can get the course object by using Moodle's get_course method.
         * > ```php
         * get_course($courseID)
         * ```
         */
        function get_course_img($course)
        {
            return \core_course\external\course_summary_exporter::get_course_image($course);
        }

        /**
         * get time elapsed like "a week ago", "a month ago", etc.
         * 
         * @param string $datetime the time string
         * @return string
         * 
         * @ Glavić from: https://stackoverflow.com/questions/1416697/converting-timestamp-to-time-ago-in-php-e-g-1-day-ago-2-days-ago
         */
        function time_elapsed($datetime, $full = false)
        {

            $str_time_elapsed_year = get_string('ui_te_year', 'block_last_access_course');
            $str_time_elapsed_month = get_string('ui_te_month', 'block_last_access_course');
            $str_time_elapsed_week = get_string('ui_te_week', 'block_last_access_course');
            $str_time_elapsed_day = get_string('ui_te_day', 'block_last_access_course');
            $str_time_elapsed_hour = get_string('ui_te_hour', 'block_last_access_course');
            $str_time_elapsed_minute = get_string('ui_te_minute', 'block_last_access_course');
            $str_time_elapsed_second = get_string('ui_te_second', 'block_last_access_course');
            $str_time_elapsed_multi = get_string('ui_te_multi', 'block_last_access_course');
            $str_time_elapsed_ago = get_string('ui_te_ago', 'block_last_access_course');
            $str_time_elapsed_just_now = get_string('ui_te_just_now', 'block_last_access_course');
            try {
                $now = new DateTime;
                $ago = new DateTime($datetime);

                $diff = $now->diff($ago);

                $diff->w = floor($diff->d / 7);
                $diff->d -= $diff->w * 7;

                $string = array(
                    'y' => $str_time_elapsed_year,
                    'm' => $str_time_elapsed_month,
                    'w' => $str_time_elapsed_week,
                    'd' => $str_time_elapsed_day,
                    'h' => $str_time_elapsed_hour,
                    'i' => $str_time_elapsed_minute,
                    's' => $str_time_elapsed_second,
                );
                foreach ($string as $k => &$v) {
                    if ($diff->$k) {
                        $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? $str_time_elapsed_multi : '');
                    } else {
                        unset($string[$k]);
                    }
                }

                if (!$full) $string = array_slice($string, 0, 1);
                return $string ? implode(', ', $string) . ' ' . $str_time_elapsed_ago : $str_time_elapsed_just_now;
            } catch (\Throwable $th) {
                //throw new Exception($th);
                echo 'function time_elapsed(): incorrect timestamp format.';
            }
        }

        function get_content()
        {

            if ($this->content !== NULL) {
                return $this->content;
            }

            // setting default value for
            if (empty($this->config->color)) {
                $this->config->color = BLOCK_LAST_ACCESS_COURSE_BTN_TEXT_COLOR;
            }
            if (empty($this->config->background)) {
                $this->config->background = BLOCK_LAST_ACCESS_COURSE_BTN_BACKGROUND_COLOR;
            }
            if (empty($this->config->course_number)) {
                $this->config->course_number = BLOCK_LAST_ACCESS_COURSE_COURSE_NUMBER;
            }
            if (empty($this->config->thumb_background_color)) {
                $this->config->thumb_background_color = BLOCK_LAST_ACCESS_COURSE_THUMB_BACKGROUND_COLOR;
            }
            if (empty($this->config->thumb_text_color)) {
                $this->config->thumb_text_color = BLOCK_LAST_ACCESS_COURSE_THUMB_TEXT_COLOR;
            }

            if (empty($this->config->show_time_elapsed)) {
                if ($this->config->show_time_elapsed == 0) {
                    $this->config->show_time_elapsed = 0;
                } else {
                    $this->config->show_time_elapsed = BLOCK_LAST_ACCESS_COURSE_SHOW_TIME_ELAPSED;
                }
            }

            $str_show_more = get_string('ui_show_more', 'block_last_access_course');
            $str_show_less = get_string('ui_show_less', 'block_last_access_course');
            $str_view_all_courses = get_string('ui_view_all_courses', 'block_last_access_course');
            $str_msg_no_course = get_string('ui_msg_no_course', 'block_last_access_course');
            $str_msg_please_enrol = get_string('ui_msg_please_enrol', 'block_last_access_course');


            global $USER, $CFG, $DB, $OUTPUT, $PAGE;

            // ** FOR LOGGING **//
            // global $USER, $DB, $PAGE, $CFG;


            $firstname = $USER->firstname;

            // hide the block for guest users
            if ($firstname === "Guest user") return;

            //get all the last access courses
            $lastCourseAccess = $USER->lastcourseaccess;

            //if no courses, tell them to enroll
            if (empty($lastCourseAccess)) {
                $message = "<p style='text-align: center;'>{$str_msg_no_course}<br><span style='font-size: 4rem;'>😁</span><br>$str_msg_please_enrol</p>";
                $this->content->text = $message;
                return;
            }
            //sort them by value (timestamp, id=>timestamp)
            arsort($lastCourseAccess);
?>
            <style>
                .course__card {
                    display: flex;
                    min-height: 64px;
                    text-decoration: none;
                    transition: all .25s;
                    margin-bottom: .25rem;
                }

                .course__name {
                    color: black;
                    margin-bottom: 0;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                .course__card:hover {
                    background: rgba(0, 0, 0, .2);
                    text-decoration: none;
                }

                .course__card:hover .course__name {
                    text-decoration: underline;
                    text-decoration-color: black;
                }

                .course__img {
                    width: 4rem;
                    height: 4rem;
                    object-fit: contain;
                    margin-right: 1rem;
                }

                .course__thumb {
                    width: 4rem;
                    height: 4rem;
                    background: <?= $this->config->thumb_background_color ?>;
                    margin-right: 1rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .course__thumb--title {
                    font-size: 1rem;
                    margin: 0;
                    font-weight: 700;
                    color: <?= $this->config->thumb_text_color ?>;
                    overflow: hidden;
                    text-align: center;
                }

                #btnShowMore {
                    color: <?= $this->config->color ?>;
                    background: <?= $this->config->background ?>;
                    border: none;
                    transform: translateY(0);
                    min-width: 100%;
                    padding: .5rem 0;
                    filter: brightness(1);
                    transition: all .25s;
                }

                #btnShowMore.less:hover {
                    transform: translateY(-3px);
                }

                #btnShowMore:hover {
                    filter: brightness(.75);
                    transform: translateY(3px);
                }

                #btnShowMore:active {
                    filter: brightness(1);
                    transform: translateY(0);
                }

                .fxdc {
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }

                .timestamp {
                    color: rgba(0, 0, 0, .3);
                    font-size: 0.75rem;
                }
            </style>

            <?php

            $html = "<div>";
            $i = 1;
            $course_url = $CFG->wwwroot . '/course';

            //getting each course's name and putting them in li tags
            foreach ($lastCourseAccess as $courseID => $timestamp) {

                //getting a course's info
                $course = get_course($courseID);
                $course_img_url = $this->get_course_img($course);
                $course_name = $course->fullname;
                $acronym = $this->get_course_acronym($course_name);
                $time_period = $this->time_elapsed(userdate($timestamp));
                $show_time_elapsed = $this->config->show_time_elapsed == 1 ? "<p class='timestamp'>{$time_period}</p>" : "";
                // echo $this->config->show_time_elapsed;


                //getting course thumbnail. if not available, use the acronym.
                $course_thumb = (!$course_img_url) ? "<div class='course__thumb'><p class='course__thumb--title'>" . strtoupper($acronym) . "</p></div>" : "<img src='{$course_img_url}' class='course__img'>";

                $extra = ($i > $this->config->course_number) ? 'extra' : '';
                // show normal courses according to the number set in setting
                $html .= "
                    <a href='{$course_url}/view.php?id={$courseID}' title ='{$course_name}' class='course__card {$extra}'> 
                        {$course_thumb} 
                        <div class='fxdc'>
                            <p class='course__name'>{$course_name}</p>
                            {$show_time_elapsed}
                    </div>
                    </a>";
                $i++;
            }


            // won't show button if the number of courses is smaller than the number in setting
            // basically means they dont have enough courses so they won't need a button
            $btn_show_more = (count($lastCourseAccess) <= $this->config->course_number) ? "" : "<button id='btnShowMore'>{$str_show_more}</button>";

            //piecing all the things together
            $html .= "</div>{$btn_show_more}";

            //display the block
            $this->content = new stdClass;
            $this->content->text = $html;
            // $this->content->footer = "<h6 style='text-align:right;'>block made by CK.</h6>";
            $this->content->footer = "
            <h6 style='text-align:right; margin-top: 1rem;'><a href='{$course_url}'>{$str_view_all_courses}</a></h6>";
            ?>
            <script>
                window.addEventListener('load', () => {
                    const extraCourses = document.querySelectorAll(".extra");
                    const btnShow = document.querySelector("#btnShowMore");

                    btnShow.addEventListener("click", () => {
                        if (extraCourses[0].style.display === "none") {
                            courseShow();
                        } else {
                            courseHidden();
                        }
                    });

                    function courseHidden() {
                        extraCourses.forEach((extra) => {
                            extra.style.display = "none";
                            extra.style.visibility = "hidden";
                            btnShow.textContent = "<?= $str_show_more ?>";
                            btnShow.classList.remove("less");
                        });
                    }

                    function courseShow() {
                        extraCourses.forEach((extra) => {
                            extra.style.display = "flex";
                            extra.style.visibility = "visible";
                            btnShow.textContent = "<?= $str_show_less ?>";
                            btnShow.classList.add("less");
                        });
                    }

                    courseHidden();
                })
            </script>
<?php
            // finally displaying the block
            return $this->content;
        }
    }
}
