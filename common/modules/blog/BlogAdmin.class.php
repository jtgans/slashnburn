<?PHP
require_once("classes/Module.class.php");
require_once("jtlib/DataSanitizer.class.php");
require_once("Blog.class.php");
require_once("TrackBack.class.php");

class BlogAdmin extends Module
{
    var $_blog;
    
    function BlogAdmin()
    {
        $this->initModule();
        $this->_blog = new Blog();

        $this->_nav->appendSubNav("manage", "Blog", "blog");
        $this->_nav->appendSubNav("config", "Blog", "blog");
    }

    function _updateConfig($config)
    {
        $this->_config->setKey('blog/display',          $config['display']);
        $this->_config->setKey('blog/display_header',   $config['display_header']);
        $this->_config->setKey('blog/email_on_comment', $config['email_on_comment']);
        $this->_config->setKey('blog/header',           addslashes($config['header']));
    }
    
    function handlePage($args)
    {
        switch ($args[0]) {
            case "manage":
                $form = new DataSanitizer();
                $form->addField("id",             "request,integer");
                $form->addField("comment_id",     "get,integer");
                $form->addField("allow_comments", "post,integer");
                $form->addField("title",          "post,string");
                $form->addField("trackback_urls", "post,string");
                $form->addField("body",           "post,string");
                $form->addField("summary",        "post,string");
                $form->addField("mode",           "request,string,lowercase");
                $form->sanitizeData();
                
                $this->_smarty->assign("entries", $this->_blog->getEntries());
        
                switch ($form->getDataByField("mode")) {
                    case "delete_comment":
                        $form->addRequired("id");
                        $form->addRequired("comment_id");

                        if ($form->checkRequired()) {
                            $result = $this->_blog->deleteComment($form->getDataByField("comment_id"));

                            if ($result[0] != MODULE_SUCCESS) {
                                $entry = $this->_blog->getEntryByID($form->getDataByField("id"));
                                $this->_smarty->assign("entry", $entry);
                                $this->_smarty->assign("editor_value", stripslashes($entry['body']));
                                $this->_smarty->assign("errors", array("Unable to delete comment."));
                                $this->_smarty->display("admin-blog-edit.tmpl");
                            } else {
                                $entry = $this->_blog->getEntryByID($form->getDataByField("id"));
                                $this->_smarty->assign("entry", $entry);
                                $this->_smarty->assign("editor_value", stripslashes($entry['body']));
                                $this->_smarty->assign("message", "Comment deleted.");
                                $this->_smarty->display("admin-blog-edit.tmpl");
                            }
                        }
                        break;
                    
                    case "delete":
                        $form->addRequired("id");

                        if ($form->checkRequired()) {
                            $result = $this->_blog->deleteEntry($form->getDataByField("id"));
                            
                            if ($result[0] != MODULE_SUCCESS) {
                                $this->_smarty->assign("errors", array("Unable to delete entry."));
                                $this->_smarty->display("admin-blog.tmpl");
                                return $result;
                            } else {
                                $this->_smarty->assign("message", "Entry deleted.");
                                $this->_smarty->display("admin-blog.tmpl");
                                return array(MODULE_SUCCESS, "Success.");
                            }
                        }
                        break;
                        
                    case "post":
                        $form->addRequired("title");
                        $form->addRequired("body");
                        $form->checkRequired();

                        if ($form->isFormValid()) {
                            $entry_id = $this->_blog->addEntry($form->getDataByField("title"), $form->getDataByField("body"), $form->getDataByField("summary"), $form->getDataByField("allow_comments"));

//                             if ($form->getDataByField("trackback_urls")) {
//                                 $urls = explode(" ", $form->getDataByField("trackback_urls"));
                                
//                                 $trackback = new TrackBack();
//                                 if (!$trackback->sendPings($urls)) {
//                                     $errors[] = "Some trackback pings could not be sent. The pings that failed were:";

//                                     foreach ($trackback->getFailedPings() AS $url) {
//                                         $errors[] = $url; 
//                                     }

//                                     $errors[] = "";
//                                 }
//                             }

                            $this->_smarty->assign("message", "Your blog post was successful.");
                        } else {
                            if ($form->getErrorByField("title")) $errors[] = "Please provide a title for this blog post.";
                            if ($form->getErrorByField("body"))  $errors[] = "Please provide some content for this blog post.";
                            $this->_smarty->assign("errors", $errors);
                        }
                        
                        $this->_smarty->display("admin-blog.tmpl");
                        return array(MODULE_SUCCESS, "Success.");
                        break;

                    case "update":
                        $form->addRequired("id");
                        $form->addRequired("title");
                        $form->addRequired("body");
                        $form->checkRequired();

                        if ($form->isFormValid()) {
                            $this->_blog->updateEntry($form->getDataByField("id"), $form->getDataByField("title"), $form->getDataByField("body"), $form->getDataByField("summary"), $form->getDataByField("allow_comments"));
                            
                            $this->_smarty->assign("message", "Your blog edit was successful.");
                            $this->_smarty->display("admin-blog.tmpl");
                        } else {
                            if ($form->getErrorByField("id"))    $errors[] = "The blog ID to edit was missing.";
                            if ($form->getErrorByField("title")) $errors[] = "Please provide a title for this blog post.";
                            if ($form->getErrorByField("body"))  $errors[] = "Please provide some content for this blog post.";
                            
                            $this->_smarty->assign("errors", $errors);
                            $this->_smarty->assign("entry",  $this->_blog->getEntry($form->getDataByField("id")));
                            $this->_smarty->display("admin-blog-edit.tmpl");
                        }

                        return array(MODULE_SUCCESS, "Success");
                        break;

                    case "edit":
                        $form->addRequired("id");
                        $form->checkRequired();

                        if ($form->isFormValid()) {
                            $entry = $this->_blog->getEntryByID($form->getDataByField("id"));
                            $this->_smarty->assign("entry", $entry);
                            $this->_smarty->assign("editor_value", stripslashes($entry['body']));
                        }
                        
                        $this->_smarty->display("admin-blog-edit.tmpl");
                        break;

                    default:
                        $this->_smarty->display("admin-blog.tmpl");
                        break;
                }
                break;

            case "config":
                $form = new DataSanitizer();
                $form->addField("display",          "post,integer");
                $form->addField("display_header",   "post,integer");
                $form->addField("email_on_comment", "post,integer");
                $form->addField("header",           "post,string");
                $form->addRequired("display");
                $form->addRequired("display_header");
                $form->sanitizeData();

                if ($form->checkRequired()) {
                    $this->_updateConfig($form->getAllData());
                    $this->_smarty->assign("message", "Your blog configuration was updated.");
                }

                $this->_smarty->assign("header", stripslashes($this->_config->getKey("blog/header")));
                $this->_smarty->display("admin-blog-config.tmpl");
                break;

            default:
                return array(MODULE_NOTFOUND, "BlogAdmin::handlePage: Unknown mode $args[0].");
                break;
        }

        return array(MODULE_SUCCESS, "Success");
    }
}
