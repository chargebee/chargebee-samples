FROM node:18

WORKDIR /client

COPY package.json package-lock.json ./

RUN npm install

EXPOSE 8081

CMD ["npm", "run", "dev"]

