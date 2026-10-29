# FAQ Chatbot (RAG) - Laravel + free AI API

You add your own documents (FAQ, policies, product info). Then you ask a
question and the AI answers ONLY from those documents, and shows its sources.

## How RAG works here

1. Add document -> text is split into small chunks (`Chunker`).
2. Each chunk is turned into an embedding (list of numbers) by the AI API
   and saved in the database (`chunks` table).
3. You ask a question -> the question is also turned into an embedding.
4. We compare it with all chunks (cosine similarity) and take the top 4.
5. We send only those 4 chunks + the question to the chat model and ask it to
   answer from them. If the answer is not there, it says "I don't know".

No vector database is needed for a small project. Embeddings are stored as JSON
in MySQL/SQLite and compared in PHP.

## Setup

   composer create-project laravel/laravel faq-rag
   cd faq-rag

Copy `app`, `database`, `resources`, `routes` and `samples` from this zip into
the project (replace `routes/web.php`).

Add this inside the returned array in `config/services.php`:

   'ai' => [
       'base_url'         => env('AI_BASE_URL'),
       'key'              => env('AI_API_KEY'),
       'model'            => env('AI_MODEL'),
       'embed_model'      => env('AI_EMBED_MODEL'),
       'embed_dimensions' => env('AI_EMBED_DIMENSIONS'),
   ],

Add to `.env` (same as your ticket project, plus the embedding model):

   AI_BASE_URL=https://generativelanguage.googleapis.com/v1beta/openai
   AI_API_KEY=your-key
   AI_MODEL=gemini-3.5-flash-lite
   AI_EMBED_MODEL=gemini-embedding-001
   # AI_EMBED_DIMENSIONS=768

Set up your database in `.env` (MySQL recommended), then:

   php artisan migrate
   php artisan serve

## Test the embedding API first (terminal)

   curl -m 30 -s "https://generativelanguage.googleapis.com/v1beta/openai/embeddings" \
     -H "Content-Type: application/json" -H "Authorization: Bearer $KEY" \
     -d '{"model":"gemini-embedding-001","input":"hello"}' | head -c 300

If you see `"embedding":[` followed by numbers, it works.

## Try it

1. Open /documents, add `samples/sample-faq.txt` (paste its text).
2. Open / and ask: "How many days do I have to return a product?"
3. Ask something that is NOT in the document: it should say it doesn't know.

## Notes

- Free tiers have rate limits. Adding a very large document makes many embedding
  calls. Keep test documents small.
- If you change the embedding model later, delete and re-add your documents
  (vectors from different models cannot be compared).
- Do not put real customer or private data into a free tier.

## Ideas to extend

- Run indexing in a Laravel Queue job.
- Upload .txt/.pdf files instead of pasting text.
- Store chat history per user.
- Add a similarity threshold (ignore chunks with a very low score).
- Add a feature test with `Http::fake()` for the embedding and chat calls.
