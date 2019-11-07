<?php
class Message {
    private $user_obj;
    private $con;

    public function __construct($con, $user) {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function getMostRecentUser() {
        $userLoggedIn = $this->user_obj->getUsername();
        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC LIMIT 1");
        
        if (mysqli_num_rows($query) == 0) {
            return false;
        }

        $row = mysqli_fetch_array($query);
        $user_to = $row['user_to'];
        $user_from = $row['user_from'];

        if ($user_to != $userLoggedIn) {
            return $user_to;
        } else {
            return $user_from;
        }
    }

    public function sendMessage($user_to, $body, $date) {
        if ($body != "") {
            $userLoggedIn = $this->user_obj->getUsername();
            $query = mysqli_query($this->con, "INSERT INTO messages VALUES('', '$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");
        }
    }

    public function getMessages($otherUser) {
        $userLoggedIn = $this->user_obj->getUsername();
        $data = "";

        $query = mysqli_query($this->con, "UPDATE messages SET opened='yes' WHERE user_to='$userLoggedIn' AND user_from='$otherUser'");
        $get_messages_query = mysqli_query($this->con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$otherUser') OR (user_from='$userLoggedIn' AND user_to='$otherUser')");
    
        while($row = mysqli_fetch_array($get_messages_query)) {
            $user_to = $row['user_to'];
            $user_from = $row['user_from'];
            $body = $row['body'];

            $div_top = ($user_to == $userLoggedIn) ? "<div class='message' id='their_message'>" : "<div class='message' id='my_message'>";
            $data = $data . $div_top . $body . "</div><br><br>";
        }
        return $data;
    }

    public function getLatestMessage($userLoggedIn, $user2) {
        $details_array = array();
        $query = mysqli_query($this->con, "SELECT body, user_to, date FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user2') OR (user_to='$user2' AND user_from='$userLoggedIn') ORDER BY id DESC LIMIT 1");
        $row = mysqli_fetch_array($query);
        $sent_by = ($row['user_to'] == $userLoggedIn) ? "They said: " : "You said: ";

        $date_time_now = date("Y-m-d H:i:s");
        $start_date = new DateTime($row['date']);
        $end_date = new DateTime($date_time_now);
        $interval = $start_date->diff($end_date);
        if ($interval->y >= 1) {
            if ($interval == 1) {
                $time_message = $interval->y . " year ago";
            } else {
                $time_message = $interval->y . " years ago";
            }
        }
        else if ($interval->m >= 1) {
            if ($interval->d == 0) {
                $days = " ago";
            }
            else if ($interval->d == 1) {
                $days = $interval->d . " day ago";
            }
            else {
                $days = $interval->d . " days ago";
            }

            if ($interval->m == 1) {
                $time_message = $interval->m . " month". $days;
            } else {
                $time_message = $interval->m . " months". $days;
            }
        }
        else if ($interval->d >= 1) {
            if ($interval->d == 1) {
                $time_message = "Yesterday";
            } else {
                $time_message = $interval->d . " days ago";
            }
        }
        else if ($interval->h >= 1) {
            if ($interval->h == 1) {
                $time_message = $interval->h . " hour ago";
            } else {
                $time_message = $interval->h . " hours ago";
            }
        }
        else if ($interval->i >= 1) {
            if ($interval->i == 1) {
                $time_message = $interval->i . " minute ago";
            } else {
                $time_message = $interval->i . " minutes ago";
            }
        }
        else {
            if ($interval->s < 30) {
                $time_message = "Just now";
            } else {
                $time_message = $interval->s . " seconds ago";
            }
        }
        array_push($details_array, $sent_by);
        array_push($details_array, $row['body']);
        array_push($details_array, $time_message);
        return $details_array;
    }

    public function getConversations() {
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";
        $conversations = array();

        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");
        while ($row = mysqli_fetch_array($query)) {
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];
            if (!in_array($user_to_push, $conversations)) {
                array_push($conversations, $user_to_push);
            }
        }

        foreach ($conversations as $username) {
            $user_found_obj = new User($this->con, $username);
            $latest_message_details = $this->getLatestMessage($userLoggedIn, $username);
            $dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
            $split = str_split($latest_message_details[1], 12);
            $split = $split[0] . $dots;
            $return_string .= "<a href='messages.php?u=$username'><div class='user_found_messages'>
                                <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>
                                " . $user_found_obj->getFirstAndLastName() . "
                                <span class='timestamp_smaller text-muted'>" . $latest_message_details[2] . "</span>
                                <p class='m-0 text-muted'>" . $latest_message_details[0] . $split . " </p>
                                </div>
                                </a>";
        }
        return $return_string;
    }

    public function getConvosDropdown($data, $limit) {
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";
        $conversations = array();

        if ($page == 1) {
            $start = 0;
        } else {
            $start = ($page - 1) * $limit;
        }

        $set_viewed_query = mysqli_query($this->con, "UPDATE messages SET viewed='yes' WHERE user_to='$userLoggedIn'");

        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");
        while ($row = mysqli_fetch_array($query)) {
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];
            if (!in_array($user_to_push, $conversations)) {
                array_push($conversations, $user_to_push);
            }
        }

        $num_iterations = 0;
        $count = 1;

        foreach ($conversations as $username) {
            if ($num_iterations++ < $start) {
                continue;
            }
            if ($count > $limit) {
                break;
            } else {
                $count++;
            }

            $is_unread_query = mysqli_query($this->con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$username') OR (user_from='$userLoggedIn' AND user_to='$username') ORDER BY id DESC");
            $row = mysqli_fetch_array($is_unread_query);
            $style = ($row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";

            $user_found_obj = new User($this->con, $username);
            $latest_message_details = $this->getLatestMessage($userLoggedIn, $username);
            $dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
            $split = str_split($latest_message_details[1], 12);
            $split = $split[0] . $dots;

            if ($row['opened'] === 'yes' && $row['user_from'] === $userLoggedIn && $row['user_to'] === $username) {
                $latest_message_details[2] .= " ✓";
            }

            if ($row['opened'] === 'no' && $row['user_from'] === $userLoggedIn && $row['user_to'] === $username) {
                $style = "";
                $latest_message_details[2] .= " ←";
            }

            $return_string .= "<a href='messages.php?u=$username'>
                                <div class='user_found_messages' style='" . $style . "'>
                                <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>
                                " . $user_found_obj->getFirstAndLastName() . "
                                <span class='timestamp_smaller text-muted'>" . $latest_message_details[2] . "</span>
                                <p class='m-0 text-muted'>" . $latest_message_details[0] . $split . " </p>
                                </div>
                                </a>";
        }

        if ($count > $limit) {
            $return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
        } else {
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more messages to load</p>";
        }
        return $return_string;
    }

    public function getUnreadNumber() {
        $userLoggedIn = $this->user_obj->getUsername();
        $query = mysqli_query($this->con, "SELECT * FROM messages WHERE viewed='no' AND user_to='$userLoggedIn'");
        return mysqli_num_rows($query);
    }
}

?>
