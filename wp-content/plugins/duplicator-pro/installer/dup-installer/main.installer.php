<?php
/*
 * Duplicator Website Installer
 * Copyright (C) 2018, Snap Creek LLC
 * website: snapcreek.com
 *
 * Duplicator (Pro) Plugin is distributed under the GNU General Public License, Version 3,
 * June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
 * St, Fifth Floor, Boston, MA 02110, USA
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
if (!defined('DUPXABSPATH')) {
    define('DUPXABSPATH', dirname(__FILE__));
}

try {
    // for ngrok url and Local by Flywheel Live URL
    if (isset($_SERVER['HTTP_X_ORIGINAL_HOST'])) {
        $host = $_SERVER['HTTP_X_ORIGINAL_HOST'];
    } else {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']; //WAS SERVER_NAME and caused problems on some boxes
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $_SERVER ['HTTPS'] = 'on';
    }
    if (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'https') {
        $_SERVER ['HTTPS'] = 'on';
    }
    if (isset($_SERVER['HTTP_CF_VISITOR'])) {
        $visitor = json_decode($_SERVER['HTTP_CF_VISITOR']);
        if ($visitor->scheme == 'https') {
            $_SERVER ['HTTPS'] = 'on';
        }
    }    
    $serverDomain  = 'http'.((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 's' : '').'://'.$host;
    $serverUrlSelf = preg_match('/^[\\\\\/]?$/', dirname($_SERVER['SCRIPT_NAME'])) ? '' : dirname($_SERVER['SCRIPT_NAME']);

    define('DUPX_INIT', str_replace('\\', '/', dirname(__FILE__)));
    define('DUPX_INIT_URL', $serverDomain.$serverUrlSelf);
    define('DUPX_ROOT', preg_match('/^[\\\\\/]?$/', dirname(DUPX_INIT)) ? '/' : dirname(DUPX_INIT));
    define('DUPX_ROOT_URL', $serverDomain.(preg_match('/^[\\\\\/]?$/', dirname($serverUrlSelf)) ? '' : dirname($serverUrlSelf)));

    require_once(DUPX_INIT.'/classes/config/class.boot.php');

    /**
     * init constants and include
     */
    DUPX_Boot::init();
    DUPX_Log::setThrowExceptionOnError(true);
    DUPX_Log::logTime('INIT END', DUPX_Log::LV_DETAILED);

    // if is ajax always die in controller
    DUPX_Ctrl_ajax::controller();
}
catch (Exception $ex) {
    DUPX_Log::logException($ex, DUPX_Log::LV_DEFAULT, 'EXCEPTION ON INIT: ');
    ob_start();
    ?>
    <div>
        <h1>DUPLICATOR PRO: ISSUE</h1>
        Problem on duplicator init.<br>
        Message: <b><?php echo htmlspecialchars($ex->getMessage()); ?></b>
    </div>
    <?php
    $content = ob_get_clean();
    DUPX_Boot::problemLayout($content);
    die();
}

ob_start();
try {
    $controller     = DUPX_CTRL::getInstance();
    $exceptionError = false;
    // DUPX_log::error thotw an exception
    DUPX_Log::setThrowExceptionOnError(true);
    DUPX_Log::logTime('CONTROLLER START', DUPX_Log::LV_DETAILED);

    $controller->mainController();
}
catch (Exception $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    $controller->setExceptionPage($e);
}

/**
 * clean output
 */
$unespectOutput = trim(ob_get_clean());
if (!empty($unespectOutput)) {
    DUPX_Log::info('ERROR: Unespect output '.DUPX_Log::varToString($unespectOutput));
    $exceptionError = new Exception('Unespected output '.DUPX_Log::varToString($unespectOutput));
    $controller->setExceptionPage($exceptionError);
}

ob_start();
try {
    echo $controller->renderPage();
}
catch (Exception $e) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    $controller->setExceptionPage($e);
    echo $controller->renderPage();
}

