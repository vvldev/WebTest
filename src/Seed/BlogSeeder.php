<?php

declare(strict_types=1);

namespace App\Seed;

use App\Dto\NewCategoryDto;
use App\Dto\NewPostDto;
use App\Repository\CategoryRepositoryInterface;
use App\Repository\PostRepositoryInterface;
use DateTimeImmutable;
use Faker\Generator;
use RuntimeException;

final class BlogSeeder
{
    // Faker's ru_RU text comes from "Dead Souls", so the themes match it.
    private const CATEGORY_NAMES = [
        'Дорога', 'Усадьба', 'Ярмарка', 'Город', 'Губерния', 'Трактир',
        'Помещики', 'Чиновники', 'Хозяйство', 'Путешествие', 'Семья', 'Бал',
    ];
    private const MIN_PARAGRAPHS = 3;
    private const MAX_PARAGRAPHS = 6;
    private const MAX_VIEWS = 5000;
    private const TITLE_MIN_LENGTH = 25;
    private const TITLE_MAX_LENGTH = 100;
    private const DESCRIPTION_MAX_LENGTH = 220;
    private const SENTENCE_ATTEMPTS = 50;

    public function __construct(
        private readonly Generator $faker,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly PostRepositoryInterface $postRepository,
        private readonly PostImageGenerator $imageGenerator,
    ) {
    }

    public function run(int $categoryCount, int $postCount, int $maxCategoriesPerPost): void
    {
        $this->clear();
        $categoryIds = $this->createCategories($categoryCount);

        // The last category deliberately gets no posts: the home page must hide it.
        $this->createPosts(array_slice($categoryIds, 0, -1), $postCount, $maxCategoriesPerPost);
    }

    private function clear(): void
    {
        $this->postRepository->deleteAll();
        $this->categoryRepository->deleteAll();
        $this->imageGenerator->deleteAll();
    }

    /**
     * @return list<int>
     */
    private function createCategories(int $count): array
    {
        $categoryIds = [];
        foreach ($this->faker->randomElements(self::CATEGORY_NAMES, $count) as $name) {
            $categoryIds[] = $this->categoryRepository->add(new NewCategoryDto(
                $name,
                $this->sentence(self::TITLE_MIN_LENGTH, self::DESCRIPTION_MAX_LENGTH),
            ));
        }

        return $categoryIds;
    }

    /**
     * @param list<int> $categoryIds
     */
    private function createPosts(array $categoryIds, int $count, int $maxCategoriesPerPost): void
    {
        $maxCategories = min($maxCategoriesPerPost, count($categoryIds));
        for ($i = 0; $i < $count; $i++) {
            $this->postRepository->add(new NewPostDto(
                $this->title(),
                $this->sentence(self::TITLE_MIN_LENGTH, self::DESCRIPTION_MAX_LENGTH),
                $this->content(),
                $this->imageGenerator->generate(),
                $this->faker->numberBetween(0, self::MAX_VIEWS),
                DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-1 year')),
                $this->faker->randomElements($categoryIds, $this->faker->numberBetween(1, $maxCategories)),
            ));
        }
    }

    /**
     * A whole sentence; a final period looks odd in a heading, "?" and "!" stay.
     */
    private function title(): string
    {
        return rtrim($this->sentence(self::TITLE_MIN_LENGTH, self::TITLE_MAX_LENGTH), '.');
    }

    /**
     * Paragraphs are separated by an empty line, as the post page expects.
     */
    private function content(): string
    {
        $paragraphs = [];
        $paragraphCount = $this->faker->numberBetween(self::MIN_PARAGRAPHS, self::MAX_PARAGRAPHS);
        for ($i = 0; $i < $paragraphCount; $i++) {
            $paragraphs[] = $this->faker->realText(600);
        }

        return implode("\n\n", $paragraphs);
    }

    /**
     * Picks one complete sentence of Faker text: starts with a capital, ends with . ! ? or ….
     */
    private function sentence(int $minLength, int $maxLength): string
    {
        for ($attempt = 0; $attempt < self::SENTENCE_ATTEMPTS; $attempt++) {
            foreach ($this->completeSentences($this->faker->realText(600)) as $sentence) {
                $length = mb_strlen($sentence);
                if ($length >= $minLength && $length <= $maxLength) {
                    return $sentence;
                }
            }
        }

        throw new RuntimeException('Faker produced no sentence of suitable length');
    }

    /**
     * @return list<string>
     */
    private function completeSentences(string $text): array
    {
        $parts = preg_split('/(?<=[.!?…])\s+/u', $text) ?: [];
        // Faker cuts the text at a length limit, so the last part may be a broken sentence.
        array_pop($parts);

        $sentences = [];
        foreach ($parts as $part) {
            // Dialogue dashes and spaces at the start are not part of the sentence.
            $sentence = (string) preg_replace('/^[\s\p{Pd}]+/u', '', $part);
            // Quotes are skipped: a sentence cut out of a quotation keeps only half of them.
            if (preg_match('/^\p{Lu}[^«»"„“]*[.!?…]$/u', $sentence) === 1) {
                $sentences[] = $sentence;
            }
        }

        return $sentences;
    }
}
