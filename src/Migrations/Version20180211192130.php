<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180211192130 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE articles_tags_bridge (article_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_A89685CE7294869C (article_id), INDEX IDX_A89685CEBAD26311 (tag_id), PRIMARY KEY(article_id, tag_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE articles_tags_bridge ADD CONSTRAINT FK_A89685CE7294869C FOREIGN KEY (article_id) REFERENCES news (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE articles_tags_bridge ADD CONSTRAINT FK_A89685CEBAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE articles_tags_bridge DROP FOREIGN KEY FK_A89685CE7294869C');
        $this->addSql('ALTER TABLE articles_tags_bridge DROP FOREIGN KEY FK_A89685CEBAD26311');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE articles_tags_bridge');
        $this->addSql('DROP TABLE tags');
    }
}
