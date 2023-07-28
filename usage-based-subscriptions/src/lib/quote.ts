// Array of inspirational quotes
const inspirationalQuotes = [
  'The only way to do great work is to love what you do. - Steve Jobs',
  "Believe you can and you're halfway there. - Theodore Roosevelt",
  'The future belongs to those who believe in the beauty of their dreams. - Eleanor Roosevelt',
  'Success is not final, failure is not fatal: It is the courage to continue that counts. - Winston Churchill',
  'It does not matter how slowly you go as long as you do not stop. - Confucius',
  "Your time is limited, don't waste it living someone else's life. - Steve Jobs",
  'The best way to predict the future is to create it. - Peter Drucker',
  'Believe in yourself and all that you are. Know that there is something inside you that is greater than any obstacle. - Christian D. Larson',
  'Your life does not get better by chance, it gets better by change. - Jim Rohn',
  'The only limit to our realization of tomorrow will be our doubts of today. - Franklin D. Roosevelt',
  'In the middle of difficulty lies opportunity. - Albert Einstein',
  'The only place where success comes before work is in the dictionary. - Vidal Sassoon',
];

export function getRandomInspirationalQuote() {
  const randomIndex = Math.floor(Math.random() * inspirationalQuotes.length);
  return inspirationalQuotes[randomIndex];
}
