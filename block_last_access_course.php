<?php

/**
 * Contains the class for the Timeline block.
 *
 * @package    block_last_access_course
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * made by CaptKraken
 */

//settings
define('BLOCK_LAST_ACCESS_COURSE_BTN_TEXT_COLOR', "#030303");
define('BLOCK_LAST_ACCESS_COURSE_BTN_BACKGROUND_COLOR', "#f1f1f1");
define('BLOCK_LAST_ACCESS_COURSE_THUMB_BACKGROUND_COLOR', "#c3c3c3");
define('BLOCK_LAST_ACCESS_COURSE_THUMB_TEXT_COLOR', "#f1f1f1");
define('BLOCK_LAST_ACCESS_COURSE_COURSE_NUMBER', 3);

//checking protocol to build a link later for courses
// define('BLOCK_SRC_DIR', $CFG->wwwroot . "/blocks/last_access_course/src/");


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

            global $USER, $CFG;

            // ** FOR LOGGING **//
            // global $USER, $DB, $PAGE, $CFG;
            // echo "</br></br></br>";
            // print_object($CFG);
            // print_object($USER);
            // $course_four = $DB->get_record('course', array('id' => 4));
            // print_r($course_four);
            // print_object(get_course_image());

            $firstname = $USER->firstname;

            // hide the block for guest users
            if ($firstname === "Guest user") return;

            //get all the last access courses
            $lastCourseAccess = $USER->lastcourseaccess;

            //if no courses, tell them to enroll
            if (empty($lastCourseAccess)) {
                $message = "<p style='text-align: center;'>You haven't enrolled in any course.<br><span style='font-size: 4rem;'>😁</span><br>Enroll in some.</p>";
                $this->content->text = $message;
                return;
            }
            //sort them by value (timestamp, id=>timestamp)
            arsort($lastCourseAccess);
?>
            <style>
                .course__card {
                    display: flex;
                    align-items: center;
                    min-height: 64px;
                    text-decoration: none;
                    transition: all .25s;
                    margin-bottom: .25rem;
                }

                .course__name {
                    color: black;
                    margin-bottom: 0;
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
                    filter: brightness(1.25);
                    transform: translateY(3px);
                }

                #btnShowMore:active {
                    filter: brightness(1);
                    transform: translateY(0);
                }
            </style>

            <?php

            $html = "<div>";
            $i = 1;
            $course_url = $CFG->wwwroot . '/course/view.php?id=';

            //getting each course's name and putting them in li tags
            foreach ($lastCourseAccess as $courseID => $value) {

                //getting a course's info
                $course = get_course($courseID);
                $course_name = $course->fullname;

                //getting course image https://stackoverflow.com/questions/61818875/how-to-get-course-image-from-moodle-api
                $course_img_url = \core_course\external\course_summary_exporter::get_course_image($course);

                //getting course acronym
                //from https://stackoverflow.com/questions/9706429/get-the-first-letter-of-each-word-in-a-string
                $words = explode(" ", $course_name);
                $acronym = "";

                foreach ($words as $w) {
                    $acronym .= $w[0];
                }

                //getting course thumbnail. if not available, use the acronym.
                $course_thumb = (!$course_img_url) ? "<div class='course__thumb'><p class='course__thumb--title'>" . strtoupper($acronym) . "</p></div>" : "<img src='{$course_img_url}' class='course__img'>";


                // echo "<img src='{$course_img_url}' style='width: 4rem; height: 4rem; object-fit: contain; '><br>";

                $extra = ($i > $this->config->course_number) ? 'extra' : '';
                // show normal courses according to the number set in setting
                $html .= "
                    <a href='{$course_url}{$courseID}' class='course__card {$extra}'> 
                        {$course_thumb} 
                        <p class='course__name'>{$course_name}</p>
                    </a>";
                $i++;
            }


            // won't show button if the number of courses is smaller than the number in setting
            // basically means they dont have enough courses so they won't need a button
            $btn_show_more = (count($lastCourseAccess) <= $this->config->course_number) ? "" : "<button id='btnShowMore'>Show More ↓</button>";

            //piecing all the things together
            $final_html = $html . "</div>{$btn_show_more}";

            //display the block
            $this->content = new stdClass;
            $this->content->text = $final_html;
            // $this->content->footer = "<h6 style='text-align:right;'>block made by CK.</h6>";
            $this->content->footer = "
            <h6 style='text-align:right; margin-top: 1rem;'><a href='{$this->config->moodle_dir}/course'>View All Courses</a></h6>";
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
                            btnShow.textContent = "Show More ↓";
                            btnShow.classList.remove("less");
                        });
                    }

                    function courseShow() {
                        extraCourses.forEach((extra) => {
                            extra.style.display = "flex";
                            extra.style.visibility = "visible";
                            btnShow.textContent = "Show Less ↑";
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
