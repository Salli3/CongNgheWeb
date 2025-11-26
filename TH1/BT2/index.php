<?php

$quizFile = __DIR__ . '/quiz.txt';

$raw = file_get_contents($quizFile);

$blocks = preg_split("/\r?\n\s*\r?\n/", trim($raw));

$questions = [];
foreach ($blocks as $b) {
    $lines = preg_split("/\r?\n/", trim($b));
    if (count($lines) == 0) continue;

    $answerLine = null;
    foreach ($lines as $i => $ln) {
        if (preg_match('/^\s*ANSWER\s*:\s*(.+)$/i', $ln, $m)) {
            $answerLine = trim($m[1]);
            unset($lines[$i]);
            break;
        }
    }

    $lines = array_values($lines);
    if (count($lines) == 0) continue;

    $questionText = array_shift($lines);
    $options = [];

    foreach ($lines as $ln) {
        if (preg_match('/^\s*([A-Z])\s*[\.\)]\s*(.+)$/u', $ln, $m)) {
            $options[$m[1]] = trim($m[2]);
        }
    }

    $correct = [];
    if ($answerLine !== null) {
        
        $parts = explode(',', $answerLine);
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '') continue;
            
            $correct[] = strtoupper($p[0]);
        }
    }

    if (!empty($questionText) && !empty($options)) {
        $questions[] = [
            'question' => $questionText,
            'options' => $options,
            'answer' => $correct
        ];
    }
}


$results = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userAnswers = $_POST['q'] ?? [];
    $total = count($questions);
    $correctCount = 0;
    $details = [];

    foreach ($questions as $i => $q) {
        $qid = (string)$i;
        $correct = $q['answer'];
        $ua = [];

        if (isset($userAnswers[$qid])) {
            if (is_array($userAnswers[$qid])) {
                foreach ($userAnswers[$qid] as $v) {
                    if (preg_match('/([A-Z])/i', $v, $m)) $ua[] = strtoupper($m[1]);
                }
            } else {
                if (preg_match('/([A-Z])/i', $userAnswers[$qid], $m)) $ua[] = strtoupper($m[1]);
            }
        }

        sort($ua);
        $c = $correct;
        sort($c);

        $isCorrect = ($ua === $c);
        if ($isCorrect) $correctCount++;

        $details[] = [
            'question' => $q['question'],
            'correct' => $correct,
            'user' => $ua,
            'isCorrect' => $isCorrect,
            'options' => $q['options']
        ];
    }

    $results = [
        'total' => $total,
        'correctCount' => $correctCount,
        'details' => $details
    ];
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Bài thi trắc nghiệm</title>
<style>
body { font-family: Arial, sans-serif; max-width:900px; margin:20px auto; padding:0 16px; }
.q { border:1px solid #ddd; padding:12px; margin-bottom:12px; border-radius:6px; background:#fafafa; }
.opts { margin-top:8px; }
.opt { margin:6px 0; }
.correct { color: green; font-weight:bold; }
.wrong { color: red; font-weight:bold; }
.result { padding:12px; background:#f0f8ff; margin-bottom:20px; border-radius:6px; }
button { padding:8px 16px; font-size:16px; margin-top:12px; cursor:pointer; }
</style>
</head>
<body>

<h1>Bài thi trắc nghiệm</h1>
<p>Hệ thống đọc câu hỏi từ file <code>quiz.txt</code>.</p>

<?php if ($results !== null): ?>
    <div class="result">
        <h2>Kết quả</h2>
        <p>Đúng: <strong><?php echo $results['correctCount']; ?></strong> / <?php echo $results['total']; ?></p>
    </div>

    <?php foreach ($results['details'] as $i => $d): ?>
        <div class="q">
            <div><strong>Câu <?php echo $i+1; ?>:</strong> <?php echo htmlspecialchars($d['question']); ?></div>
            <div class="opts">
                <?php foreach ($d['options'] as $letter => $text): ?>
                    <div class="opt">
                        <strong><?php echo $letter; ?>.</strong> <?php echo htmlspecialchars($text); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <p>
                <span>Đáp án đúng: <strong><?php echo implode(', ', $d['correct']); ?></strong></span><br>
                <span>Đáp án bạn chọn: <strong><?php echo empty($d['user']) ? '-' : implode(', ', $d['user']); ?></strong></span>
            </p>
            <?php if ($d['isCorrect']): ?>
                <p class="correct">Đúng</p>
            <?php else: ?>
                <p class="wrong">Sai</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <p><a href="index.php">Làm lại</a></p>

<?php else: ?>

<form method="post" action="">
    <?php foreach ($questions as $i => $q):
        $multiple = count($q['answer']) > 1;
        $qid = $i;
    ?>
        <div class="q">
            <div><strong>Câu <?php echo $i+1; ?>:</strong> <?php echo htmlspecialchars($q['question']); ?></div>
            <div class="opts">
                <?php foreach ($q['options'] as $letter => $text): ?>
                    <div class="opt">
                        <label>
                        <?php if ($multiple): ?>
                            <input type="checkbox" name="q[<?php echo $qid; ?>][]" value="<?php echo $letter; ?>">
                        <?php else: ?>
                            <input type="radio" name="q[<?php echo $qid; ?>]" value="<?php echo $letter; ?>">
                        <?php endif; ?>
                        <strong><?php echo $letter; ?>.</strong> <?php echo htmlspecialchars($text); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <button type="submit">Nộp bài</button>
</form>

<?php endif; ?>

</body>
</html>
