<?php
/*
Mail address Cleaner: A Roundcube plugin to remove others than email address specifications
Copyright (C) 2013 Mikihiko Mori

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of  MERCHANTABILITY or FITNESS FOR
A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <http://www.gnu.org/licenses/>.
*/

class address_cleaner extends rcube_plugin
{
  public $task = 'mail';

  function init()
  {
    $this->add_hook('message_outgoing_headers',
                    array($this, 'filter_dest_addr'));
  }

  function filter_dest_addr($args)
  {
    global $OUTPUT, $EMAIL_FORMAT_ERROR;
    $headers = $args['headers'];
    $new_headers = array();

    foreach($headers as $k => $v){
      if(in_array($k, array('To', 'Cc', 'Bcc'))){
        $new_headers[$k] = $this->cleanup_addr($v);
        if ($EMAIL_FORMAT_ERROR) { # may not be used this block
          $OUTPUT->show_message('emailformaterror', 'error', array('email' => $EMAIL_FORMAT_ERROR));
          $OUTPUT->send('iframe');
        }
      }else{
        $new_headers[$k] = $v;
      }
    }

    $args['headers'] = $new_headers;
    return $args;
  }

/**
 * This function cleanup_addr() has been used a large part of function
 * rcmail_email_input_format() in program/steps/mail/sendmail.inc of
 * Roundcube core at version 1.3.3.
 */
  function cleanup_addr($mailto, $check=true)
  {
    global $RCMAIL, $EMAIL_FORMAT_ERROR;

    // simplified email regexp, supporting quoted local part
    $email_regexp = '(\S+|("[^"]+"))@\S+';

    $delim   = trim($RCMAIL->config->get('recipients_separator', ','));
    $regexp  = array("/[,;$delim]\s*[\r\n]+/", '/[\r\n]+/', "/[,;$delim]\s*\$/m", '/;/', '/(\S{1})(<'.$email_regexp.'>)/U');
    $replace = array($delim.' ', ', ', '', $delim, '\\1 \\2');

    // replace new lines and strip ending ', ', make address input more valid
    $mailto = trim(preg_replace($regexp, $replace, $mailto));
    $items  = rcube_utils::explode_quoted_string($delim, $mailto);
    $result = array();

    foreach ($items as $item) {
        $item = trim($item);
        // address in brackets without name (do nothing)
        if (preg_match('/^<'.$email_regexp.'>$/', $item)) {
            $item     = rcube_utils::idn_to_ascii(trim($item, '<>'));
            $result[] = $item;
        }
        // address without brackets and without name (add brackets)
        else if (preg_match('/^'.$email_regexp.'$/', $item)) {
            $item     = rcube_utils::idn_to_ascii($item);
            $result[] = $item;
        }
        // address with name (handle name)
        else if (preg_match('/<*'.$email_regexp.'>*$/', $item, $matches)) {
            $address = $matches[0];
            $name    = trim(str_replace($address, '', $item));
            if ($name[0] == '"' && $name[count($name)-1] == '"') {
                $name = substr($name, 1, -1);
            }
            $name     = stripcslashes($name);
            $address  = rcube_utils::idn_to_ascii(trim($address, '<>'));
            $result[] = $address;
            $item     = $address;
        }

        // check address format
        $item = trim($item, '<>');
        if ($item && $check && !rcube_utils::check_email($item)) {
            $EMAIL_FORMAT_ERROR = $item;
            return;
        }
    }

    return implode(', ', $result);
  }
}

?>
