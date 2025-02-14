FROM node:18

WORKDIR /app

COPY package.json pnpm-lock.yaml ./

RUN npm install

EXPOSE 3000

CMD ["npm", "run", "dev"]

