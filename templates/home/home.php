<h1>Mon blog</h1>
<p>En construction</p>
<?php
// foreach ($posts as $post)
// {
    ?>
    <div>
        <h2><a href="../public/index.php?route=post&postId=<?= htmlspecialchars($post->getId());?>"><?= htmlspecialchars($post->getTitle());?></a></h2>
        <p>Intro : <?= htmlspecialchars($post->getShortText());?></p>
        <p>Contenu : <?= htmlspecialchars($post->getText());?></p>
        <p>Id : <?= htmlspecialchars($post->getUserId());?></p>
        <p>Créé le : <?= htmlspecialchars($post->getCreatedAt()->format('Y-m-d H:i:s'));?></p>
    </div>
    <br>
    <?php
// }
?>